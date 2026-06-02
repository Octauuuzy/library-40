<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Log;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $selectedKategori = $request->get('kategori');
        $user = Auth::user();
        $anggotaId = $user->anggota_id;

        $query = Buku::with('kategoris')
            ->withCount('favorits')
            ->withExists(['favorits as is_favorited' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);

        if ($anggotaId) {
            $query->withExists(['peminjamans as is_borrowed_by_user' => function ($query) use ($anggotaId) {
                $query->where('anggota_id', $anggotaId)->active();
            }]);
        }

        if ($selectedKategori) {
            $query->whereHas('kategoris', function ($q) use ($selectedKategori) {
                $q->where('kategoris.id', $selectedKategori);
            });
        }

        $bukus = $query->get();
        $activePeminjamanByBook = collect();
        
        // Calculate fine for the current user based on active loans
        $denda = 0;
        if ($anggotaId) {
            $denda = Peminjaman::where('anggota_id', $anggotaId)
                ->active()
                ->sum('denda');

            $activePeminjamanByBook = Peminjaman::query()
                ->select('id', 'buku_id')
                ->where('anggota_id', $anggotaId)
                ->active()
                ->orderByDesc('id')
                ->get()
                ->unique('buku_id')
                ->keyBy('buku_id');
        }
                    
        $kategoris = Kategori::has('bukus')->take(10)->get();

        return view('katalog.index', compact('bukus', 'denda', 'kategoris', 'selectedKategori', 'activePeminjamanByBook'));
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

        $sedangDipinjam = Peminjaman::where('buku_id', $buku->id)
            ->active()
            ->count();
        $sisaStok = $buku->stok - $sedangDipinjam;

        if ($sisaStok <= 0) {
            return response()->json(['success' => false, 'message' => 'Stok buku ini sedang habis.']);
        }

        // Check if user already borrowed this book and hasn't returned it
        $alreadyBorrowed = Peminjaman::where('anggota_id', $user->anggota_id)
            ->where('buku_id', $buku->id)
            ->active()
            ->exists();

        if ($alreadyBorrowed) {
            return response()->json(['success' => false, 'message' => 'Anda masih meminjam buku ini.']);
        }

        $peminjaman = Peminjaman::create([
            'anggota_id' => $user->anggota_id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_rencana' => now()->addDays($request->durasi),
            'status' => Peminjaman::STATUS_DIPINJAM,
            'denda' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dipinjam hingga ' . $peminjaman->tgl_kembali_rencana->format('d M Y') . '.',
        ]);
    }

    public function kembalikanBuku(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
        ]);

        $user = Auth::user();

        if (!$user->anggota_id) {
            return response()->json([
                'success' => false,
                'message' => 'Data anggota Anda belum lengkap.',
            ], 422);
        }

        $peminjaman = Peminjaman::query()
            ->whereKey($request->peminjaman_id)
            ->where('anggota_id', $user->anggota_id)
            ->with('buku')
            ->first();

        if (!$peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan.',
            ], 404);
        }

        if (!$peminjaman->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Buku ini sudah dikembalikan sebelumnya.',
            ], 422);
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DIKEMBALIKAN,
            'tgl_kembali_aktual' => now(),
        ]);

        Log::create([
            'user_id' => $user->id,
            'username' => $user->username ?? $user->name,
            'deskripsi' => 'Mengembalikan buku: ' . ($peminjaman->buku->judul ?? 'Tanpa Judul') . '. ID Pinjam: ' . $peminjaman->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dikembalikan.',
            'status' => $peminjaman->fresh()->status_label,
        ]);
    }
}
