<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * GET /api/notifikasi
     * Ambil semua notifikasi milik user yang sedang login,
     * paling baru di atas, dengan pagination 20 per halaman.
     */
    public function index(Request $request)
    {
        $notifikasi = Notifikasi::forUser(Auth::id())
            ->with('peminjaman')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $notifikasi,
        ]);
    }

    /**
     * GET /api/notifikasi/unread
     * Ambil 5 notifikasi terbaru yang belum dibaca (untuk dropdown lonceng).
     * Sertakan juga jumlah total unread.
     */
    public function getUnread()
    {
        $userId = Auth::id();

        $terbaru = Notifikasi::forUser($userId)
            ->belumDibaca()
            ->with('peminjaman')
            ->latest()
            ->take(5)
            ->get();

        $totalUnread = Notifikasi::forUser($userId)
            ->belumDibaca()
            ->count();

        return response()->json([
            'success'      => true,
            'data'         => $terbaru,
            'total_unread' => $totalUnread,
        ]);
    }

    /**
     * PATCH /api/notifikasi/{id}/read
     * Tandai satu notifikasi sebagai sudah dibaca.
     * User hanya boleh menandai notifikasi miliknya sendiri.
     */
    public function markRead(Notifikasi $notifikasi)
    {
        // Otorisasi: pastikan notifikasi ini milik user yang login
        if ($notifikasi->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan mengakses notifikasi ini.',
            ], 403);
        }

        $notifikasi->update(['dibaca' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
            'data'    => $notifikasi,
        ]);
    }

    /**
     * POST /api/notifikasi/read-all
     * Tandai SEMUA notifikasi milik user sebagai sudah dibaca.
     * Catat ke log aktivitas.
     */
    public function markAllRead()
    {
        $userId = Auth::id();

        $jumlah = Notifikasi::forUser($userId)
            ->belumDibaca()
            ->count();

        Notifikasi::forUser($userId)
            ->belumDibaca()
            ->update(['dibaca' => true]);

        // Catat ke log aktivitas (gunakan model LogAktivitas yang sudah ada)
        if ($jumlah > 0) {
            LogAktivitas::create([
                'user_id'   => $userId,
                'aksi'      => 'update',
                'tabel'     => 'notifikasi',
                'data_lama' => null,
                'data_baru' => json_encode(['aksi' => 'tandai_semua_dibaca', 'jumlah' => $jumlah]),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Semua notifikasi ({$jumlah}) ditandai sudah dibaca.",
        ]);
    }

    /**
     * DELETE /api/notifikasi/{id}
     * Hapus satu notifikasi milik user.
     * Catat ke log aktivitas.
     */
    public function destroy(Notifikasi $notifikasi)
    {
        // Otorisasi
        if ($notifikasi->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan menghapus notifikasi ini.',
            ], 403);
        }

        // Catat ke log sebelum dihapus
        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aksi'      => 'delete',
            'tabel'     => 'notifikasi',
            'data_lama' => json_encode($notifikasi->toArray()),
            'data_baru' => null,
        ]);

        $notifikasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus.',
        ]);
    }

    /**
     * DELETE /api/notifikasi/hapus-semua
     * Hapus semua notifikasi yang sudah dibaca milik user (bersihkan riwayat).
     */
    public function destroyAll()
    {
        $userId = Auth::id();

        $jumlah = Notifikasi::forUser($userId)->where('dibaca', true)->count();

        Notifikasi::forUser($userId)->where('dibaca', true)->delete();

        LogAktivitas::create([
            'user_id'   => $userId,
            'aksi'      => 'delete',
            'tabel'     => 'notifikasi',
            'data_lama' => json_encode(['aksi' => 'hapus_semua_dibaca', 'jumlah' => $jumlah]),
            'data_baru' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menghapus {$jumlah} notifikasi.",
        ]);
    }
}
