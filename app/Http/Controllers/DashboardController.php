<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function root(Request $request): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        return redirect()->route('dashboard');
    }

    public function dashboard(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->level === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    public function admin(Request $request): Response
    {
        abort_unless($request->user()?->level === 'admin', 403);

        $stats = [
            'total_buku' => DB::table('buku')->count(),
            'total_anggota' => DB::table('user')->where('level', 'anggota')->count(),
            'total_kategori' => DB::table('kategori')->count(),
            'total_peminjaman' => DB::table('peminjaman')->count(),
            'sedang_dipinjam' => DB::table('peminjaman')->where('status_peminjaman', 'dipinjam')->count(),
            'sudah_dikembalikan' => DB::table('peminjaman')->where('status_peminjaman', 'dikembalikan')->count(),
        ];

        $recentPeminjaman = DB::table('peminjaman as p')
            ->join('user as u', 'p.id_user', '=', 'u.id_user')
            ->join('buku as b', 'p.id_buku', '=', 'b.id_buku')
            ->select('p.*', 'u.nama', 'b.judul')
            ->orderByDesc('p.id_peminjaman')
            ->limit(5)
            ->get();

        return response()->view('dashboard.admin', [
            'user' => $request->user(),
            'stats' => $stats,
            'recentPeminjaman' => $recentPeminjaman,
        ]);
    }

    public function user(Request $request): Response
    {
        abort_unless($request->user()?->level === 'anggota', 403);

        $user = $request->user();
        $filterKategori = (int) $request->integer('kategori');
        $search = trim((string) $request->string('q'));

        $greeting = $this->greeting();

        $kategoris = DB::table('kategori')
            ->orderBy('nama_kategori')
            ->get();

        $setting = DB::table('setting')
            ->whereIn('nama', ['toleransi_hari', 'denda_per_hari'])
            ->pluck('nilai', 'nama');

        $toleransi = (int) ($setting['toleransi_hari'] ?? 0);
        $dendaPerHari = (int) ($setting['denda_per_hari'] ?? 0);

        $pinjamanAktif = DB::table('peminjaman')
            ->where('id_user', $user->id_user)
            ->where('status_peminjaman', 'dipinjam')
            ->get();

        $totalDenda = 0;
        foreach ($pinjamanAktif as $pinjaman) {
            $selisih = now()->startOfDay()->diffInDays($pinjaman->tanggal_pengembalian, false);
            $hariTelat = (-1 * $selisih) - $toleransi;
            if ($hariTelat > 0) {
                $totalDenda += $hariTelat * $dendaPerHari;
            }
        }

        $query = DB::table('buku as b')
            ->join('kategori as k', 'b.id_kategori', '=', 'k.id_kategori')
            ->select('b.*', 'k.nama_kategori');

        if ($filterKategori > 0) {
            $query->where('b.id_kategori', $filterKategori);
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('b.judul', 'like', "%{$search}%")
                    ->orWhere('b.penulis', 'like', "%{$search}%");
            });
        }

        $books = $query
            ->orderByDesc('b.id_buku')
            ->get()
            ->map(function (object $book) use ($user) {
                $activeBorrows = DB::table('peminjaman')
                    ->where('id_buku', $book->id_buku)
                    ->where('status_peminjaman', 'dipinjam')
                    ->count();

                $book->star_count = DB::table('favorit')
                    ->where('id_buku', $book->id_buku)
                    ->count();

                $book->user_starred = DB::table('favorit')
                    ->where('id_user', $user->id_user)
                    ->where('id_buku', $book->id_buku)
                    ->exists();

                $book->is_borrowed = DB::table('peminjaman')
                    ->where('id_user', $user->id_user)
                    ->where('id_buku', $book->id_buku)
                    ->where('status_peminjaman', 'dipinjam')
                    ->exists();

                $book->sisa_stok = max(0, (int) $book->stok - $activeBorrows);
                $coverFilename = $this->coverFilename($book->id_buku);
                $book->cover_url = $coverFilename ? route('cover.show', ['filename' => $coverFilename]) : null;

                return $book;
            });

        return response()->view('dashboard_user', [
            'user' => $user,
            'greeting' => $greeting,
            'kategoris' => $kategoris,
            'books' => $books,
            'filterKategori' => $filterKategori,
            'search' => $search,
            'totalDenda' => $totalDenda,
        ]);
    }

    public function cover(string $filename): BinaryFileResponse
    {
        abort_unless($filename !== 'placeholder', 404);

        $path = realpath(base_path('perpus/cover/' . $filename));
        $root = realpath(base_path('perpus/cover'));

        abort_unless(
            $path !== false
            && $root !== false
            && str_starts_with($path, $root . DIRECTORY_SEPARATOR)
            && is_file($path),
            404
        );

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function greeting(): string
    {
        $hour = (int) now()->format('H');

        return match (true) {
            $hour >= 5 && $hour < 12 => 'Selamat Pagi',
            $hour >= 12 && $hour < 15 => 'Selamat Siang',
            $hour >= 15 && $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
    }

    private function coverFilename(int $bookId): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            $filename = "{$bookId}.{$extension}";
            if (is_file(base_path('perpus/cover/' . $filename))) {
                return $filename;
            }
        }

        return null;
    }
}
