<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengembalianResource extends JsonResource
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
            'peminjaman_id' => $this->peminjaman_id,
            'peminjaman' => new PeminjamanResource($this->whenLoaded('peminjaman')),
            'tanggal_kembali' => $this->tanggal_kembali?->format('Y-m-d'),
            'kondisi_alat' => $this->kondisi_alat,
            'keterangan' => $this->keterangan,
            'denda' => $this->denda,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
