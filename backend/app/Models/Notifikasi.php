<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'peminjaman_id',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    /* ── Relasi ── */

    /** Penerima notifikasi */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Peminjaman terkait (nullable) */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /* ── Scope ── */

    /** Hanya notifikasi yang belum dibaca */
    public function scopeBelumDibaca($query)
    {
        return $query->where('dibaca', false);
    }

    /** Filter per user */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
