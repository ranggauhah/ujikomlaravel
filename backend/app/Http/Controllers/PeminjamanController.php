<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian']);

        // Filter by user if role peminjam
        if ($request->user()->role === 'peminjam') {
            $query->where('user_id', $request->user()->id);
        }

        $peminjamans = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => PeminjamanResource::collection($peminjamans),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeminjamanRequest $request)
    {
        try {
            DB::beginTransaction();

            // Validasi stok alat
            foreach ($request->detail as $item) {
                $alat = Alat::findOrFail($item['alat_id']);
                if ($alat->jumlah < $item['jumlah_pinjam']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$alat->nama_alat} tidak mencukupi. Tersedia: {$alat->jumlah}",
                    ], 400);
                }
            }

            // Buat peminjaman — status 'menunggu' menunggu persetujuan petugas/admin
            $peminjaman = Peminjaman::create([
                'user_id'        => $request->user()->id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'keperluan'      => $request->keperluan,
                'status'         => 'menunggu',
            ]);

            // Buat detail peminjaman — stok BELUM dikurangi, baru dikurangi saat approve
            foreach ($request->detail as $item) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $item['alat_id'],
                    'jumlah_pinjam' => $item['jumlah_pinjam'],
                ]);
            }

            DB::commit();

            $peminjaman->load(['user', 'detailPinjam.alat']);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dibuat',
                'data' => new PeminjamanResource($peminjaman),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'detailPinjam.alat', 'pengembalian']);

        return response()->json([
            'success' => true,
            'data' => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:dipinjam,dikembalikan,terlambat',
        ]);

        $peminjaman->update($validated);
        $peminjaman->load(['user', 'detailPinjam.alat']);

        return response()->json([
            'success' => true,
            'message' => 'Status peminjaman berhasil diupdate',
            'data' => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Approve peminjaman — digunakan oleh Petugas & Admin.
     * Mengubah status dari 'menunggu' ke 'dipinjam'.
     */
    public function approve(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya peminjaman berstatus "menunggu" yang bisa disetujui',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Validasi dan kurangi stok saat disetujui
            $peminjaman->load('detailPinjam');
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                if ($alat->jumlah < $detail->jumlah_pinjam) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$alat->nama_alat} tidak mencukupi. Tersedia: {$alat->jumlah}",
                    ], 400);
                }
                $alat->update(['jumlah' => $alat->jumlah - $detail->jumlah_pinjam]);
            }

            $peminjaman->update(['status' => 'dipinjam']);

            DB::commit();

            $peminjaman->load(['user', 'detailPinjam.alat']);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil disetujui',
                'data'    => new PeminjamanResource($peminjaman),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tolak peminjaman — digunakan oleh Petugas & Admin.
     * Mengubah status ke 'ditolak' dan mengembalikan stok.
     */
    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya peminjaman berstatus "menunggu" yang bisa ditolak',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Kembalikan stok
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::find($detail->alat_id);
                if ($alat) {
                    $alat->update(['jumlah' => $alat->jumlah + $detail->jumlah_pinjam]);
                }
            }

            $peminjaman->update(['status' => 'ditolak']);

            DB::commit();

            $peminjaman->load(['user', 'detailPinjam.alat']);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil ditolak',
                'data'    => new PeminjamanResource($peminjaman),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peminjaman $peminjaman)
    {
        try {
            DB::beginTransaction();

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::find($detail->alat_id);
                $alat->update([
                    'jumlah' => $alat->jumlah + $detail->jumlah_pinjam,
                ]);
            }

            $peminjaman->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
