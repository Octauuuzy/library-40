<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBuku = Buku::count();
        $totalAnggota = User::count();
        $totalKategori = Kategori::count();
        
        $sedangDipinjam = Peminjaman::where('status', 'Dipinjam')->count();
        $dikembalikan = Peminjaman::where('status', 'Dikembalikan')->count();
        $totalPeminjaman = Peminjaman::count();
        
        $peminjamanTerbaru = Peminjaman::with(['user', 'buku'])->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalBuku',
            'totalAnggota',
            'totalKategori',
            'sedangDipinjam',
            'dikembalikan',
            'totalPeminjaman',
            'peminjamanTerbaru'
        ));
    }
}
