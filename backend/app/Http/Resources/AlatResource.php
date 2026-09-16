<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori_id' => $this->kategori_id,
            'kategori' => new KategoriAlatResource($this->whenLoaded('kategori')),
            'nama_alat' => $this->nama_alat,
            'merk' => $this->merk,
            'jumlah' => $this->jumlah,
            'deskripsi' => $this->deskripsi,
            'kondisi' => $this->kondisi,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
