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

        $alreadyRequested = \App\Models\PermintaanPeminjaman::where('anggota_id', $user->anggota_id)
            ->where('buku_id', $buku->id)
            ->where('jenis', \App\Models\PermintaanPeminjaman::JENIS_PINJAM)
            ->where('status', \App\Models\PermintaanPeminjaman::STATUS_PENDING)
            ->exists();

        if ($alreadyRequested) {
            return response()->json(['success' => false, 'message' => 'Anda sudah mengajukan peminjaman untuk buku ini. Menunggu persetujuan admin.']);
        }

        \App\Models\PermintaanPeminjaman::create([
            'user_id' => $user->id,
            'anggota_id' => $user->anggota_id,
            'buku_id' => $buku->id,
            'jenis' => \App\Models\PermintaanPeminjaman::JENIS_PINJAM,
            'durasi' => $request->durasi,
            'status' => \App\Models\PermintaanPeminjaman::STATUS_PENDING
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dikirim. Menunggu persetujuan admin.',
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

        $alreadyRequested = \App\Models\PermintaanPeminjaman::where('anggota_id', $user->anggota_id)
            ->where('peminjaman_id', $peminjaman->id)
            ->where('jenis', \App\Models\PermintaanPeminjaman::JENIS_KEMBALIKAN)
            ->where('status', \App\Models\PermintaanPeminjaman::STATUS_PENDING)
            ->exists();

        if ($alreadyRequested) {
            return response()->json(['success' => false, 'message' => 'Anda sudah mengajukan pengembalian untuk buku ini. Menunggu persetujuan admin.']);
        }

        \App\Models\PermintaanPeminjaman::create([
            'user_id' => $user->id,
            'anggota_id' => $user->anggota_id,
            'buku_id' => $peminjaman->buku_id,
            'peminjaman_id' => $peminjaman->id,
            'jenis' => \App\Models\PermintaanPeminjaman::JENIS_KEMBALIKAN,
            'status' => \App\Models\PermintaanPeminjaman::STATUS_PENDING
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pengembalian berhasil dikirim. Menunggu persetujuan admin.',
            'status' => $peminjaman->fresh()->status_label,
        ]);
    }
}
