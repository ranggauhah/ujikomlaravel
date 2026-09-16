<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'tanggal_pinjam' => $this->tanggal_pinjam?->format('Y-m-d'),
            'tanggal_kembali' => $this->tanggal_kembali?->format('Y-m-d'),
            'status' => $this->status,
            'keperluan' => $this->keperluan,
            'detail_pinjam' => DetailPinjamResource::collection($this->whenLoaded('detailPinjam')),
            'pengembalian' => new PengembalianResource($this->whenLoaded('pengembalian')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
