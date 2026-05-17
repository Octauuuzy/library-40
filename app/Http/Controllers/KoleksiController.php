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

        $query = Peminjaman::where('anggota_id', Auth::id())->with('buku.kategoris');

        if ($statusFilter) {
            if ($statusFilter == 'dipinjam') {
                $query->where('status', 'Dipinjam');
            } elseif ($statusFilter == 'dikembalikan') {
                $query->where('status', 'Dikembalikan');
            }
        }

        // Get unique books from user's borrowings
        $peminjamans = $query->orderBy('created_at', 'desc')->get();

        return view('koleksi.index', compact('peminjamans', 'statusFilter'));
    }
}
