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
     * PEMINJAM: Ajukan pengembalian alat.
     *
     * Hanya mencatat bahwa alat telah diserahkan.
     * Kondisi alat & finalisasi stok dilakukan oleh Petugas via update().
     * Status peminjaman diubah ke 'menunggu_pengecekan' (bukan 'dikembalikan').
     */
    public function store(StorePengembalianRequest $request)
    {
        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($request->peminjaman_id);
            $authUser   = $request->user();

            // Peminjam hanya boleh mengembalikan miliknya sendiri
            if ($authUser->role === 'peminjam' && $peminjaman->user_id !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya bisa mengembalikan peminjaman milik Anda sendiri.',
                ], 403);
            }

            // Hanya bisa diajukan jika sedang dipinjam atau terlambat
            if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya peminjaman berstatus "dipinjam" atau "terlambat" yang bisa dikembalikan.',
                ], 400);
            }

            // Cegah duplikasi
            if ($peminjaman->pengembalian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengembalian untuk peminjaman ini sudah diajukan.',
                ], 400);
            }

            // Buat record pengembalian — kondisi & denda belum diisi, menunggu petugas
            $pengembalian = Pengembalian::create([
                'peminjaman_id'    => $peminjaman->id,
                'status_pengecekan'=> 'menunggu_pengecekan',
                'tanggal_kembali'  => $request->tanggal_kembali,
                'kondisi_alat'     => 'baik',   // placeholder default, petugas yang ganti
                'keterangan'       => $request->keterangan,
                'denda'            => 0,
            ]);

            // Tandai peminjaman sedang menunggu pengecekan petugas
            // Gunakan status 'dikembalikan' agar tampilan tahu alat sudah diserahkan,
            // tapi status_pengecekan di pengembalian = 'menunggu_pengecekan' menandai belum diverifikasi.
            $peminjaman->update([
                'status'          => 'dikembalikan',
                'tanggal_kembali' => $request->tanggal_kembali,
            ]);

            DB::commit();

            $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat']);

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian berhasil diajukan. Menunggu pengecekan petugas.',
                'data'    => new PengembalianResource($pengembalian),
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
     * PETUGAS / ADMIN: Verifikasi kondisi alat setelah peminjam menyerahkan.
     *
     * Ini adalah langkah finalisasi:
     * - Petugas memilih kondisi_alat (baik / rusak)
     * - Jika rusak → isi denda
     * - Stok alat dikembalikan di sini (bukan di store)
     * - status_pengecekan → 'selesai'
     */
    public function update(Request $request, Pengembalian $pengembalian)
    {
        $validated = $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak',
            'keterangan'   => 'nullable|string',
            'denda'        => 'nullable|numeric|min:0',
        ]);

        // Jika sudah selesai diverifikasi, tolak update ulang
        if ($pengembalian->status_pengecekan === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian ini sudah diverifikasi.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($pengembalian->peminjaman_id);

            // Kembalikan stok & update kondisi alat berdasarkan hasil pengecekan petugas
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::find($detail->alat_id);
                if (!$alat) continue;

                if ($validated['kondisi_alat'] === 'baik') {
                    $alat->update([
                        'jumlah'  => $alat->jumlah + $detail->jumlah_pinjam,
                    ]);
                } else {
                    // Rusak: stok kembali tapi tandai alat rusak
                    $alat->update([
                        'jumlah'  => $alat->jumlah + $detail->jumlah_pinjam,
                        'kondisi' => 'rusak',
                    ]);
                }
            }

            // Simpan hasil verifikasi
            $pengembalian->update([
                'kondisi_alat'     => $validated['kondisi_alat'],
                'keterangan'       => $validated['keterangan'] ?? $pengembalian->keterangan,
                'denda'            => $validated['kondisi_alat'] === 'rusak'
                                        ? ($validated['denda'] ?? 0)
                                        : 0,
                'status_pengecekan'=> 'selesai',
            ]);

            DB::commit();

            $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat']);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi pengembalian berhasil disimpan.',
                'data'    => new PengembalianResource($pengembalian),
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
    public function destroy(Pengembalian $pengembalian)
    {
        $pengembalian->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengembalian berhasil dihapus',
        ]);
    }
}
