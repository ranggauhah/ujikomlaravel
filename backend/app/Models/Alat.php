<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'merk',
        'jumlah',
        'deskripsi',
        'kondisi',
        'foto',
    ];

    /**
     * Relasi ke KategoriAlat
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id');
    }

    /**
     * Relasi ke DetailPinjam
     */
    public function detailPinjam()
    {
        return $this->hasMany(DetailPinjam::class, 'alat_id');
    }
}
