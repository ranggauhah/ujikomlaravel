<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class LaporanPeminjamanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected string $tanggalMulai;
    protected string $tanggalSelesai;
    protected $data;

    public function __construct(string $tanggalMulai, string $tanggalSelesai)
    {
        $this->tanggalMulai  = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;

        $this->data = Peminjaman::with(['user', 'detailPinjam.alat.kategori', 'pengembalian'])
            ->whereBetween('tanggal_pinjam', [$tanggalMulai, $tanggalSelesai])
            ->latest()
            ->get();
    }

    public function collection()
    {
        return $this->data;
    }

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

    public function map($row): array
    {
        static $no = 0;
        $no++;

        // Ambil detail alat dari relasi
        $alatNames   = $row->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') ?: '-';
        $kategori    = $row->detailPinjam->pluck('alat.kategori.nama_kategori')->filter()->unique()->join(', ') ?: '-';
        $jumlahTotal = $row->detailPinjam->sum('jumlah_pinjam') ?: '-';

        // Hitung durasi
        $durasi = '-';
        if ($row->tanggal_pinjam && $row->tanggal_kembali) {
            $durasi = $row->tanggal_pinjam->diffInDays($row->tanggal_kembali);
        }

        return [
            $no,
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

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->data->count() + 4; // +4: title rows + heading row

        // ── BARIS 1: Judul laporan ──────────────────────────────────
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'LAPORAN PEMINJAMAN ALAT');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // ── BARIS 2: Sub-judul periode ─────────────────────────────
        $sheet->mergeCells('A2:N2');
        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($this->tanggalMulai)) . ' s/d ' . date('d/m/Y', strtotime($this->tanggalSelesai)));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6366F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // ── BARIS 3: Summary ──────────────────────────────────────
        $total       = $this->data->count();
        $dikembalikan = $this->data->where('status', 'dikembalikan')->count();
        $dipinjam    = $this->data->where('status', 'dipinjam')->count();
        $terlambat   = $this->data->where('status', 'terlambat')->count();
        $totalDenda  = $this->data->sum(fn($p) => $p->pengembalian->denda ?? 0);

        $sheet->mergeCells('A3:N3');
        $sheet->setCellValue('A3',
            "Total: {$total} transaksi   |   Dikembalikan: {$dikembalikan}   |   Dipinjam: {$dipinjam}   |   Terlambat: {$terlambat}   |   Total Denda: Rp " . number_format($totalDenda, 0, ',', '.')
        );
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['argb' => 'FF374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEDE9FE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // ── BARIS 4: Kosong ──────────────────────────────────────
        $sheet->getRowDimension(4)->setRowHeight(6);

        // ── BARIS 5: Header tabel ────────────────────────────────
        $sheet->getStyle('A5:N5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E1B4B']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF4F46E5']],
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(30);

        // ── BARIS DATA: zebra stripe + border ─────────────────────
        for ($r = 6; $r <= $lastRow; $r++) {
            $bgColor = ($r % 2 === 0) ? 'FFF5F3FF' : 'FFFFFFFF';
            $sheet->getStyle("A{$r}:N{$r}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']],
                ],
                'font'      => ['size' => 10],
            ]);

            // Warnai kolom Status (L = kolom 12)
            $statusCell = "L{$r}";
            $statusVal  = strtolower($sheet->getCell($statusCell)->getValue());
            $statusColor = match ($statusVal) {
                'dikembalikan' => ['bg' => 'FFD1FAE5', 'fg' => 'FF065F46'],
                'terlambat'    => ['bg' => 'FFFEE2E2', 'fg' => 'FF991B1B'],
                default        => ['bg' => 'FFFEF3C7', 'fg' => 'FF92400E'],
            };
            $sheet->getStyle($statusCell)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $statusColor['bg']]],
                'font'      => ['bold' => true, 'color' => ['argb' => $statusColor['fg']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Kolom No & ID center
        $sheet->getStyle("A6:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Kolom Jumlah & Durasi & Denda center
        $sheet->getStyle("G6:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K6:K{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("N6:N{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Freeze header row
        $sheet->freezePane('A6');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 8,   // ID
            'C' => 22,  // Nama
            'D' => 28,  // Email
            'E' => 30,  // Alat
            'F' => 18,  // Kategori
            'G' => 8,   // Jumlah
            'H' => 30,  // Keperluan
            'I' => 14,  // Tgl Pinjam
            'J' => 14,  // Tgl Kembali
            'K' => 10,  // Durasi
            'L' => 14,  // Status
            'M' => 14,  // Kondisi
            'N' => 14,  // Denda
        ];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}
