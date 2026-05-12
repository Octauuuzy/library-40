<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $anggotas = User::paginate(15);
        $title = 'Data Anggota';
        return view('anggota.index', compact('anggotas', 'title'));
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
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'nullable|string|max:20',
            'role' => 'required|in:admin,anggota',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        if ($request->role === 'anggota') {
            \App\Models\Anggota::create([
                'id' => $user->id,
                'nis' => str_pad($user->id, 7, '0', STR_PAD_LEFT), // auto generated nis
                'nama' => $request->name,
                'kelas' => '-',
                'no_hp' => $request->no_hp,
                'alamat' => '-'
            ]);
        }

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menambahkan anggota baru: ' . $request->username
        ]);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan.');
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
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'no_hp' => 'nullable|string|max:20',
            'role' => 'required|in:admin,anggota',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        if ($user->role === 'anggota') {
            $anggota = \App\Models\Anggota::find($id);
            if ($anggota) {
                $anggota->update([
                    'nama' => $request->name,
                    'no_hp' => $request->no_hp,
                ]);
            } else {
                \App\Models\Anggota::create([
                    'id' => $user->id,
                    'nis' => str_pad($user->id, 7, '0', STR_PAD_LEFT),
                    'nama' => $request->name,
                    'kelas' => '-',
                    'no_hp' => $request->no_hp,
                    'alamat' => '-'
                ]);
            }
        } else {
            \App\Models\Anggota::where('id', $id)->delete();
        }

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Memperbarui data anggota: ' . $user->username
        ]);

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menghapus anggota: ' . $user->username
        ]);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus.');
    }
}
