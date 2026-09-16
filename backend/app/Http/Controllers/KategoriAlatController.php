<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriAlatRequest;
use App\Http\Requests\UpdateKategoriAlatRequest;
use App\Http\Resources\KategoriAlatResource;
use App\Models\KategoriAlat;

class KategoriAlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = KategoriAlat::latest()->get();

        return response()->json([
            'success' => true,
            'data' => KategoriAlatResource::collection($kategoris),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKategoriAlatRequest $request)
    {
        $kategori = KategoriAlat::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => new KategoriAlatResource($kategori),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriAlat $kategoriAlat)
    {
        return response()->json([
            'success' => true,
            'data' => new KategoriAlatResource($kategoriAlat),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKategoriAlatRequest $request, KategoriAlat $kategoriAlat)
    {
        $kategoriAlat->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diupdate',
            'data' => new KategoriAlatResource($kategoriAlat),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriAlat $kategoriAlat)
    {
        $kategoriAlat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
