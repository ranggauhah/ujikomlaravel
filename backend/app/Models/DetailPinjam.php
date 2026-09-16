<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPinjam extends Model
{
    use HasFactory;

    protected $table = 'detail_pinjam';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah_pinjam',
    ];

    /**
     * Relasi ke Peminjaman
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Relasi ke Alat
     */
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}
