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
        
        $sedangDipinjam = Peminjaman::active()->count();
        $dikembalikan = Peminjaman::returned()->count();
        $totalPeminjaman = Peminjaman::count();
        
        $peminjamanTerbaru = Peminjaman::with(['anggota', 'buku'])->latest()->take(5)->get();

        $bukuPopuler = Buku::withCount('peminjamans')
            ->has('peminjamans')
            ->orderByDesc('peminjamans_count')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalBuku',
            'totalAnggota',
            'totalKategori',
            'sedangDipinjam',
            'dikembalikan',
            'totalPeminjaman',
            'peminjamanTerbaru',
            'bukuPopuler'
        ));
    }
}
