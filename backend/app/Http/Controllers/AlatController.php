<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alats = Alat::with('kategori')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => AlatResource::collection($alats),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlatRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('alat', $filename, 'public');
            $data['foto'] = $path;
        }

        $alat = Alat::create($data);
        $alat->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil ditambahkan',
            'data' => new AlatResource($alat),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Alat $alat)
    {
        $alat->load('kategori');

        return response()->json([
            'success' => true,
            'data' => new AlatResource($alat),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlatRequest $request, Alat $alat)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($alat->foto) {
                Storage::disk('public')->delete($alat->foto);
            }
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('alat', $filename, 'public');
            $data['foto'] = $path;
        } else {
            // Jangan overwrite foto lama jika tidak ada file baru
            unset($data['foto']);
        }

        $alat->update($data);
        $alat->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil diupdate',
            'data' => new AlatResource($alat),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alat $alat)
    {
        // Hapus file foto dari storage jika ada
        if ($alat->foto) {
            Storage::disk('public')->delete($alat->foto);
        }

        $alat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil dihapus',
        ]);
    }
}
