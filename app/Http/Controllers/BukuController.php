<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BukuController extends Controller
{
    public function index()
    {
        $bukus = Buku::with('kategoris')
            ->withCount(['peminjamans as dipinjam_count' => function ($query) {
                $query->where('status', 'Dipinjam');
            }])
            ->paginate(15);
        $kategoris = Kategori::all();
        $title = 'Data Buku';
        return view('buku.index', compact('bukus', 'kategoris', 'title'));
    }

    public function create()
    {
        // Not used anymore as we use modal
        return redirect()->route('buku.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tahun_terbit' => 'required|digits:4|integer',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
        ]);

        $data = $request->except(['cover', 'kategori_id']);

        if ($request->hasFile('cover')) {
            $destinationPath = public_path('assets/covers');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            $imageName = time() . '.' . $request->cover->extension();  
            $request->cover->move($destinationPath, $imageName);
            $data['cover'] = 'assets/covers/' . $imageName;
        }

        $buku = Buku::create($data);
        $buku->kategoris()->attach($request->kategori_id);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menambahkan buku baru: ' . $request->judul
        ]);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Buku $buku)
    {
        //
    }

    public function edit(Buku $buku)
    {
        // Not used anymore as we use modal
        return redirect()->route('buku.index');
    }

    public function update(Request $request, Buku $buku)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tahun_terbit' => 'required|digits:4|integer',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
        ]);

        $data = $request->except(['cover', 'kategori_id']);

        if ($request->hasFile('cover')) {
            if ($buku->cover && file_exists(public_path($buku->cover))) {
                unlink(public_path($buku->cover));
            }
            $destinationPath = public_path('assets/covers');
            if (!\Illuminate\Support\Facades\File::exists($destinationPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($destinationPath, 0755, true, true);
            }
            $imageName = time() . '.' . $request->cover->extension();  
            $request->cover->move($destinationPath, $imageName);
            $data['cover'] = 'assets/covers/' . $imageName;
        }

        $buku->update($data);
        $buku->kategoris()->sync([$request->kategori_id]);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Memperbarui data buku: ' . $buku->judul
        ]);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku)
    {
        if ($buku->cover && file_exists(public_path($buku->cover))) {
            unlink(public_path($buku->cover));
        }
        $buku->delete();

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menghapus buku: ' . $buku->judul
        ]);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
