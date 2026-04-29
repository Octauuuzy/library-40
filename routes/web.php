<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin' ? redirect()->route('dashboard') : redirect()->route('katalog');
    }
    return redirect()->route('login');
});

Route::get('/katalog', function () {
    return "<h1>Katalog Buku</h1><p>Selamat datang Anggota! Halaman ini sedang dalam pengembangan.</p><form method='POST' action='".route('logout')."'>".csrf_field()."<button type='submit'>Logout</button></form>";
})->middleware(['auth', 'verified'])->name('katalog');

Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('buku', BukuController::class);
    Route::resource('anggota', AnggotaController::class);
    Route::resource('kategori', KategoriController::class);
    
    // Peminjaman Routes
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::post('/peminjaman/{peminjaman}/return', [PeminjamanController::class, 'returnBook'])->name('peminjaman.return');
    Route::post('/peminjaman/settings', [PeminjamanController::class, 'updateSetting'])->name('peminjaman.settings');
    
    // Logs Routes
    Route::get('/logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
    Route::delete('/logs/clear', [\App\Http\Controllers\LogController::class, 'clear'])->name('logs.clear');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
