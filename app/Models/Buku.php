<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $fillable = ['kategori_id', 'cover', 'judul', 'pengarang', 'tahun_terbit', 'stok'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
