<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KatalogController extends Controller
{
    public function index()
    {
        // Get all books with their categories
        $bukus = Buku::with('kategoris')->get();
        
        // Calculate fine for the current user based on active loans
        $denda = Peminjaman::where('anggota_id', Auth::id())
                    ->where('status', 'dipinjam')
                    ->sum('denda');
                    
        $kategoris = Kategori::all();

        return view('katalog.index', compact('bukus', 'denda', 'kategoris'));
    }
}
