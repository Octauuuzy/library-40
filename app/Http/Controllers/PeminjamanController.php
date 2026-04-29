<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Update all late fines before displaying
        $setting = Setting::first() ?? Setting::create(['toleransi_hari' => 1, 'denda_per_hari' => 5000]);
        $now = Carbon::now('Asia/Jakarta');

        $activePeminjamans = Peminjaman::where('status', 'Dipinjam')->get();
        foreach ($activePeminjamans as $pinjam) {
            $batasKembali = Carbon::parse($pinjam->tanggal_kembali)->startOfDay();
            $hariTerlambat = $now->copy()->startOfDay()->diffInDays($batasKembali, false);
            
            // diffInDays gives negative number if $now is greater than $batasKembali
            if ($hariTerlambat < -$setting->toleransi_hari) {
                // Number of days late minus tolerance
                $hariKenaDenda = abs($hariTerlambat) - $setting->toleransi_hari;
                $denda = $hariKenaDenda * $setting->denda_per_hari;
                $pinjam->update(['denda' => $denda]);
            }
        }

        $peminjamans = Peminjaman::with(['user', 'buku'])->latest()->paginate(15);
        $title = 'Data Peminjaman';
        return view('peminjaman.index', compact('peminjamans', 'title', 'setting'));
    }

    /**
     * Update the status of the specified resource (Mark as Returned).
     */
    public function returnBook(string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status === 'Dipinjam') {
            $peminjaman->update([
                'status' => 'Dikembalikan',
                'tgl_dikembalikan' => Carbon::now('Asia/Jakarta')
            ]);
            
            return redirect()->route('peminjaman.index')->with('success', 'Status buku berhasil diubah menjadi Dikembalikan.');
        }
        
        return redirect()->route('peminjaman.index')->with('error', 'Buku sudah dikembalikan sebelumnya.');
    }

    /**
     * Update application settings (fine and tolerance).
     */
    public function updateSetting(Request $request)
    {
        $request->validate([
            'toleransi_hari' => 'required|integer|min:0',
            'denda_per_hari' => 'required|integer|min:0',
        ]);

        $setting = Setting::first();
        if ($setting) {
            $setting->update([
                'toleransi_hari' => $request->toleransi_hari,
                'denda_per_hari' => $request->denda_per_hari
            ]);
        } else {
            Setting::create($request->only('toleransi_hari', 'denda_per_hari'));
        }

        return redirect()->route('peminjaman.index')->with('success', 'Pengaturan denda dan toleransi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
