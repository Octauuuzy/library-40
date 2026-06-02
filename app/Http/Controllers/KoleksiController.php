<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KoleksiController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');

        $anggotaId = Auth::user()->anggota_id;
        $query = Peminjaman::where('anggota_id', $anggotaId)->with('buku.kategoris');

        if ($statusFilter) {
            if ($statusFilter == 'dipinjam') {
                $query->active();
            } elseif ($statusFilter == 'dikembalikan') {
                $query->returned();
            }
        }

        // Get unique books from user's borrowings
        $peminjamans = $query->orderBy('created_at', 'desc')->get();

        return view('koleksi.index', compact('peminjamans', 'statusFilter'));
    }
}
