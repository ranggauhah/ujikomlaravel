<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPeminjamanExport;
use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Laporan peminjaman berdasarkan periode
     */
    public function peminjaman(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereBetween('tanggal_pinjam', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->get();

        $summary = [
            'total_peminjaman' => $peminjamans->count(),
            'status_dipinjam' => $peminjamans->where('status', 'dipinjam')->count(),
            'status_dikembalikan' => $peminjamans->where('status', 'dikembalikan')->count(),
            'status_terlambat' => $peminjamans->where('status', 'terlambat')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'periode' => [
                    'mulai' => $request->tanggal_mulai,
                    'selesai' => $request->tanggal_selesai,
                ],
                'summary' => $summary,
                'peminjaman' => $peminjamans,
            ],
        ]);
    }

    /**
     * Laporan pengembalian berdasarkan periode
     */
    public function pengembalian(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat'])
            ->whereBetween('tanggal_kembali', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->get();

        $summary = [
            'total_pengembalian' => $pengembalians->count(),
            'kondisi_baik' => $pengembalians->where('kondisi_alat', 'baik')->count(),
            'kondisi_rusak' => $pengembalians->where('kondisi_alat', 'rusak')->count(),
            'total_denda' => $pengembalians->sum('denda'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'periode' => [
                    'mulai' => $request->tanggal_mulai,
                    'selesai' => $request->tanggal_selesai,
                ],
                'summary' => $summary,
                'pengembalian' => $pengembalians,
            ],
        ]);
    }

    /**
     * Laporan stok alat
     */
    public function stokAlat()
    {
        $alats = Alat::with('kategori')->get();

        $summary = [
            'total_kategori' => $alats->pluck('kategori_id')->unique()->count(),
            'total_jenis_alat' => $alats->count(),
            'total_stok' => $alats->sum('jumlah'),
            'alat_rusak' => $alats->where('kondisi', 'rusak')->count(),
            'alat_dalam_perbaikan' => $alats->where('kondisi', 'dalam_perbaikan')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'alat' => $alats,
            ],
        ]);
    }

    /**
     * Laporan user/peminjam
     */
    public function user()
    {
        $users = User::with(['peminjaman'])->get();

        $summary = [
            'total_user' => $users->count(),
            'admin' => $users->where('role', 'admin')->count(),
            'petugas' => $users->where('role', 'petugas')->count(),
            'peminjam' => $users->where('role', 'peminjam')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'users' => $users,
            ],
        ]);
    }

    /**
     * Export laporan peminjaman ke Excel (.xlsx)
     */
    public function exportPeminjaman(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $filename = 'laporan_peminjaman_'
            . $request->tanggal_mulai . '_sd_'
            . $request->tanggal_selesai . '.xlsx';

        return Excel::download(
            new LaporanPeminjamanExport($request->tanggal_mulai, $request->tanggal_selesai),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    /**
     * Laporan log aktivitas
     */
    public function logAktivitas(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'user_id' => 'nullable|exists:users,id',
            'aksi' => 'nullable|in:create,update,delete',
        ]);

        $query = LogAktivitas::with('user');

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('created_at', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->aksi) {
            $query->where('aksi', $request->aksi);
        }

        $logs = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
