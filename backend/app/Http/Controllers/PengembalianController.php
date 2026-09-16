<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengembalianRequest;
use App\Http\Resources\PengembalianResource;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => PengembalianResource::collection($pengembalians),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengembalianRequest $request)
    {
        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($request->peminjaman_id);

            // Peminjam hanya boleh mengembalikan miliknya sendiri
            $authUser = $request->user();
            if ($authUser->role === 'peminjam' && $peminjaman->user_id !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya bisa mengembalikan peminjaman milik Anda sendiri.',
                ], 403);
            }

            // Hanya bisa dikembalikan jika status dipinjam
            if ($peminjaman->status !== 'dipinjam') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya peminjaman berstatus "dipinjam" yang bisa dikembalikan.',
                ], 400);
            }

            // Cek apakah sudah dikembalikan
            if ($peminjaman->pengembalian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman ini sudah dikembalikan.',
                ], 400);
            }

            // Buat pengembalian
            $pengembalian = Pengembalian::create($request->validated());

            // Update status peminjaman
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => $request->tanggal_kembali,
            ]);

            // Kembalikan stok alat (hanya jika kondisi baik)
            if ($request->kondisi_alat === 'baik') {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = Alat::find($detail->alat_id);
                    $alat->update([
                        'jumlah' => $alat->jumlah + $detail->jumlah_pinjam,
                    ]);
                }
            } else {
                // Jika rusak, update kondisi alat
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = Alat::find($detail->alat_id);
                    $alat->update([
                        'jumlah' => $alat->jumlah + $detail->jumlah_pinjam,
                        'kondisi' => 'rusak',
                    ]);
                }
            }

            DB::commit();

            $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat']);

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian berhasil dicatat',
                'data' => new PengembalianResource($pengembalian),
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
    public function show(Pengembalian $pengembalian)
    {
        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat']);

        return response()->json([
            'success' => true,
            'data' => new PengembalianResource($pengembalian),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengembalian $pengembalian)
    {
        $validated = $request->validate([
            'kondisi_alat' => 'sometimes|in:baik,rusak',
            'keterangan' => 'nullable|string',
            'denda' => 'sometimes|numeric|min:0',
        ]);

        $pengembalian->update($validated);
        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat']);

        return response()->json([
            'success' => true,
            'message' => 'Data pengembalian berhasil diupdate',
            'data' => new PengembalianResource($pengembalian),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengembalian $pengembalian)
    {
        $pengembalian->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengembalian berhasil dihapus',
        ]);
    }
}
