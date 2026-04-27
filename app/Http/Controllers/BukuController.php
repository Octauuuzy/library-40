<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bukus = Buku::latest()->get();

        return view('buku.index', compact('bukus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('buku.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        Buku::create($data);

        return redirect()
            ->route('buku.index')
            ->with('success', 'Data buku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku $buku)
    {
        return redirect()->route('buku.edit', $buku);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku)
    {
        return view('buku.edit', compact('buku'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Buku $buku)
    {
        $data = $this->validatedData($request);

        $buku->update($data);

        return redirect()
            ->route('buku.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()
            ->route('buku.index')
            ->with('success', 'Data buku berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'digits:4', 'min:1000', 'max:9999'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);
    }
}
