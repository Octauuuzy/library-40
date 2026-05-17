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

        $query = Buku::with('kategoris')
            ->withCount('favorits')
            ->withExists(['favorits as is_favorited' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);

        if ($selectedKategori) {
            $query->whereHas('kategoris', function ($q) use ($selectedKategori) {
                $q->where('kategoris.id', $selectedKategori);
            });
        }

        $bukus = $query->get();
        
        // Calculate fine for the current user based on active loans
        $denda = 0;
        if (Auth::user()->anggota_id) {
            $denda = Peminjaman::where('anggota_id', Auth::user()->anggota_id)
                        ->where('status', 'Dipinjam')
                        ->sum('denda');
        }
                    
        $kategoris = Kategori::has('bukus')->take(10)->get();

        return view('katalog.index', compact('bukus', 'denda', 'kategoris', 'selectedKategori'));
    }

    public function pinjamBuku(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:bukus,id',
            'durasi' => 'required|integer|min:1|max:30',
        ]);

        $user = Auth::user();

        if (!$user->anggota_id) {
            return response()->json(['success' => false, 'message' => 'Anda harus melengkapi profil anggota sebelum dapat meminjam buku.']);
        }

        $buku = Buku::findOrFail($request->buku_id);

        $sedangDipinjam = Peminjaman::where('buku_id', $buku->id)->where('status', 'Dipinjam')->count();
        $sisaStok = $buku->stok - $sedangDipinjam;

        if ($sisaStok <= 0) {
            return response()->json(['success' => false, 'message' => 'Stok buku ini sedang habis.']);
        }

        // Check if user already borrowed this book and hasn't returned it
        $alreadyBorrowed = Peminjaman::where('anggota_id', $user->anggota_id)
            ->where('buku_id', $buku->id)
            ->where('status', 'Dipinjam')
            ->exists();

        if ($alreadyBorrowed) {
            return response()->json(['success' => false, 'message' => 'Anda masih meminjam buku ini.']);
        }

        $peminjaman = Peminjaman::create([
            'anggota_id' => $user->anggota_id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_rencana' => now()->addDays($request->durasi),
            'status' => 'Dipinjam',
            'denda' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dipinjam hingga ' . $peminjaman->tgl_kembali_rencana->format('d M Y') . '.',
        ]);
    }
}
