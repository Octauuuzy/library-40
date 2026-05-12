<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';
    
    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function bukus()
    {
        return $this->belongsToMany(Buku::class, 'buku_kategori', 'kategori_id', 'buku_id');
    }
}
