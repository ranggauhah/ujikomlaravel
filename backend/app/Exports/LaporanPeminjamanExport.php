<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanPeminjamanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected string $tanggalMulai;
    protected string $tanggalSelesai;
    protected $data;
    protected int $no = 0;

    public function __construct(string $tanggalMulai, string $tanggalSelesai)
    {
        $this->tanggalMulai   = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;

        $this->data = Peminjaman::with([
                'user',
                'detailPinjam.alat.kategori',
                'pengembalian',
            ])
            ->whereBetween('tanggal_pinjam', [$tanggalMulai, $tanggalSelesai])
            ->latest()
            ->get();
    }

    /* ────────────────────────────────────────────────────────────────
     |  DATA
     ─────────────────────────────────────────────────────────────── */

    public function collection()
    {
        return $this->data;
    }

    /* ────────────────────────────────────────────────────────────────
     |  HEADING — ditulis library di baris 1
     |  Data mulai baris 2
     |  AfterSheet akan sisipkan 4 baris di atas → heading naik ke 5
     ─────────────────────────────────────────────────────────────── */

    public function headings(): array
    {
        return [
            'No',
            'ID Peminjaman',
            'Nama Peminjam',
            'Email',
            'Alat yang Dipinjam',
            'Kategori',
            'Jumlah',
            'Keperluan',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Durasi (hari)',
            'Status',
            'Kondisi Kembali',
            'Denda (Rp)',
        ];
    }

    /* ────────────────────────────────────────────────────────────────
     |  MAPPING tiap baris data
     ─────────────────────────────────────────────────────────────── */

    public function map($row): array
    {
        $this->no++;

        $alatNames   = $row->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') ?: '-';
        $kategori    = $row->detailPinjam->pluck('alat.kategori.nama_kategori')
                           ->filter()->unique()->join(', ') ?: '-';
        $jumlahTotal = $row->detailPinjam->sum('jumlah_pinjam') ?: 0;

        $durasi = '-';
        if ($row->tanggal_pinjam && $row->tanggal_kembali) {
            $durasi = (int) $row->tanggal_pinjam->diffInDays($row->tanggal_kembali);
        }

        return [
            $this->no,
            $row->id,
            $row->user->name    ?? '-',
            $row->user->email   ?? '-',
            $alatNames,
            $kategori,
            $jumlahTotal,
            $row->keperluan     ?? '-',
            $row->tanggal_pinjam  ? $row->tanggal_pinjam->format('d/m/Y')  : '-',
            $row->tanggal_kembali ? $row->tanggal_kembali->format('d/m/Y') : '-',
            $durasi,
            strtoupper($row->status),
            $row->pengembalian->kondisi_alat ?? '-',
            $row->pengembalian->denda        ?? 0,
        ];
    }

    /* ────────────────────────────────────────────────────────────────
     |  LEBAR KOLOM
     ─────────────────────────────────────────────────────────────── */

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 10, 'C' => 24, 'D' => 30,
            'E' => 32, 'F' => 20, 'G' => 9,  'H' => 32,
            'I' => 14, 'J' => 14, 'K' => 12, 'L' => 16,
            'M' => 16, 'N' => 16,
        ];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman';
    }

    /* ────────────────────────────────────────────────────────────────
     |  AFTER SHEET — sisipkan title rows dan apply styling
     |
     |  Library menulis:
     |    Baris 1 = heading kolom
     |    Baris 2..N = data
     |
     |  Kita salin semua ke bawah 4 baris agar layout jadi:
     |    Baris 1 = Judul
     |    Baris 2 = Periode
     |    Baris 3 = Summary
     |    Baris 4 = Kosong
     |    Baris 5 = Heading kolom
     |    Baris 6.. = Data
     ─────────────────────────────────────────────────────────────── */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws */
                $ws        = $event->sheet->getDelegate();
                $dataCount = $this->data->count();
                $cols      = range('A', 'N');

                // Baris tertinggi sebelum kita ubah apa-apa
                // Heading = baris 1, data = baris 2 .. (dataCount+1)
                $origLast = $dataCount + 1;

                // ── Geser baris dari bawah ke atas (salin ke +4) ──
                for ($r = $origLast; $r >= 1; $r--) {
                    $destRow = $r + 4;
                    // Salin nilai tiap sel
                    foreach ($cols as $col) {
                        $srcVal = $ws->getCell("{$col}{$r}")->getValue();
                        $ws->setCellValue("{$col}{$destRow}", $srcVal);
                    }
                    // Salin tinggi baris
                    $ws->getRowDimension($destRow)
                       ->setRowHeight($ws->getRowDimension($r)->getRowHeight());
                }

                // ── Kosongkan baris 1-4 ──
                for ($r = 1; $r <= 4; $r++) {
                    foreach ($cols as $col) {
                        $ws->setCellValue("{$col}{$r}", '');
                    }
                }

                $lastRow = $dataCount + 5; // baris 5 = heading, data 6..lastRow

                // ── BARIS 1: Judul ──
                $ws->mergeCells('A1:N1');
                $ws->setCellValue('A1', 'LAPORAN PEMINJAMAN ALAT');
                $ws->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);
                $ws->getRowDimension(1)->setRowHeight(40);

                // ── BARIS 2: Periode ──
                $ws->mergeCells('A2:N2');
                $ws->setCellValue('A2',
                    'Periode: ' . date('d/m/Y', strtotime($this->tanggalMulai))
                    . ' s/d '  . date('d/m/Y', strtotime($this->tanggalSelesai))
                );
                $ws->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6366F1']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);
                $ws->getRowDimension(2)->setRowHeight(26);

                // ── BARIS 3: Summary ──
                $total        = $this->data->count();
                $dikembalikan = $this->data->where('status', 'dikembalikan')->count();
                $dipinjam     = $this->data->where('status', 'dipinjam')->count();
                $terlambat    = $this->data->where('status', 'terlambat')->count();
                $totalDenda   = $this->data->sum(fn($p) => $p->pengembalian->denda ?? 0);

                $ws->mergeCells('A3:N3');
                $ws->setCellValue('A3',
                    "Total: {$total} transaksi   |   Dikembalikan: {$dikembalikan}"
                    . "   |   Dipinjam: {$dipinjam}   |   Terlambat: {$terlambat}"
                    . "   |   Total Denda: Rp " . number_format($totalDenda, 0, ',', '.')
                );
                $ws->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['argb' => 'FF374151']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEDE9FE']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);
                $ws->getRowDimension(3)->setRowHeight(22);

                // ── BARIS 4: Kosong ──
                $ws->getRowDimension(4)->setRowHeight(6);

                // ── BARIS 5: Style heading kolom ──
                $ws->getStyle('A5:N5')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E1B4B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER,
                                    'wrapText'   => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                     'color'       => ['argb' => 'FF4F46E5']]],
                ]);
                $ws->getRowDimension(5)->setRowHeight(30);

                // ── BARIS 6+: Zebra stripe + border + warna status ──
                for ($r = 6; $r <= $lastRow; $r++) {
                    $bg = ($r % 2 === 0) ? 'FFF5F3FF' : 'FFFFFFFF';
                    $ws->getStyle("A{$r}:N{$r}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                         'color'       => ['argb' => 'FFD1D5DB']]],
                        'font'      => ['size' => 10],
                    ]);

                    $statusVal   = strtolower((string) $ws->getCell("L{$r}")->getValue());
                    $statusColor = match ($statusVal) {
                        'dikembalikan' => ['bg' => 'FFD1FAE5', 'fg' => 'FF065F46'],
                        'terlambat'    => ['bg' => 'FFFEE2E2', 'fg' => 'FF991B1B'],
                        default        => ['bg' => 'FFFEF3C7', 'fg' => 'FF92400E'],
                    };
                    $ws->getStyle("L{$r}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID,
                                        'startColor' => ['argb' => $statusColor['bg']]],
                        'font'      => ['bold' => true, 'color' => ['argb' => $statusColor['fg']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // ── Alignment kolom angka ──
                if ($dataCount > 0) {
                    $ws->getStyle("A6:B{$lastRow}")->getAlignment()
                       ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $ws->getStyle("G6:G{$lastRow}")->getAlignment()
                       ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $ws->getStyle("K6:K{$lastRow}")->getAlignment()
                       ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $ws->getStyle("N6:N{$lastRow}")->getAlignment()
                       ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                $ws->freezePane('A6');
            },
        ];
    }
}
