<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'bukus';
    
    protected $fillable = ['judul', 'pengarang', 'tahun_terbit', 'stok', 'cover', 'sinopsis'];

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'buku_kategori', 'buku_id', 'kategori_id');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function favorits()
    {
        return $this->hasMany(Favorit::class);
    }

    public function permintaanPeminjamans()
    {
        return $this->hasMany(PermintaanPeminjaman::class);
    }
}
