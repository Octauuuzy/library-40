<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Favorit;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $bukus = Buku::whereHas('favorits', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('kategoris')
        ->withCount('favorits')
        ->withExists(['favorits as is_favorited' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->get();

        return view('favorit.index', compact('bukus'));
    }

    public function toggle(Request $request)
    {
        \Log::info("Toggle favorit called for buku: " . $request->buku_id . " by user: " . Auth::id());

        $request->validate([
            'buku_id' => 'required|exists:bukus,id',
        ]);

        $userId = Auth::id();
        $bukuId = $request->buku_id;

        $favorit = Favorit::where('user_id', $userId)->where('buku_id', $bukuId)->first();

        if ($favorit) {
            $favorit->delete();
            $isFavorited = false;
        } else {
            Favorit::create([
                'user_id' => $userId,
                'buku_id' => $bukuId,
            ]);
            $isFavorited = true;
        }

        $count = Favorit::where('buku_id', $bukuId)->count();

        \Log::info("Favorit result: is_favorited=" . ($isFavorited ? 'true' : 'false') . ", count=" . $count);

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'count' => $count,
        ]);
    }
}
