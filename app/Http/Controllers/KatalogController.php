<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $selectedKategori = $request->get('kategori');

        $query = Buku::with('kategoris');

        if ($selectedKategori) {
            $query->whereHas('kategoris', function ($q) use ($selectedKategori) {
                $q->where('kategoris.id', $selectedKategori);
            });
        }

        $bukus = $query->get();
        
        // Calculate fine for the current user based on active loans
        $denda = Peminjaman::where('anggota_id', Auth::id())
                    ->where('status', 'dipinjam')
                    ->sum('denda');
                    
        $kategoris = Kategori::has('bukus')->take(10)->get();

        return view('katalog.index', compact('bukus', 'denda', 'kategoris', 'selectedKategori'));
    }
}
