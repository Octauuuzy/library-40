<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = \App\Models\Kategori::withCount('bukus')->get();
        $title = 'Data Kategori';
        return view('kategori.index', compact('kategoris', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategoris',
        ]);

        \App\Models\Kategori::create([
            'nama' => $request->nama,
        ]);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menambahkan kategori baru: ' . $request->nama
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = \App\Models\Kategori::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:kategoris,nama,' . $id,
        ]);

        $kategori->update([
            'nama' => $request->nama,
        ]);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Memperbarui nama kategori menjadi: ' . $request->nama
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategori = \App\Models\Kategori::findOrFail($id);
        $kategori->delete();

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menghapus kategori: ' . $kategori->nama
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
