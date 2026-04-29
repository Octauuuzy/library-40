<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::latest()->paginate(200);
        $title = 'Aktivitas Log';
        return view('logs.index', compact('logs', 'title'));
    }

    public function clear()
    {
        \App\Models\Log::truncate();

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Melakukan pembersihan seluruh data log aktivitas.'
        ]);

        return redirect()->route('logs.index')->with('success', 'Semua log aktivitas berhasil dibersihkan.');
    }
}