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

        $activePeminjamans = Peminjaman::active()->get();
        foreach ($activePeminjamans as $pinjam) {
            $batasKembali = Carbon::parse($pinjam->tgl_kembali_rencana)->startOfDay();
            $hariTerlambat = $now->copy()->startOfDay()->diffInDays($batasKembali, false);
            
            // diffInDays gives negative number if $now is greater than $batasKembali
            if ($hariTerlambat < -$setting->toleransi_hari) {
                // Number of days late minus tolerance
                $hariKenaDenda = abs($hariTerlambat) - $setting->toleransi_hari;
                $denda = $hariKenaDenda * $setting->denda_per_hari;
                $pinjam->update([
                    'status' => Peminjaman::STATUS_TERLAMBAT,
                    'denda' => $denda,
                ]);
                continue;
            }

            if ($pinjam->isLate() || $pinjam->denda !== 0) {
                $pinjam->update([
                    'status' => Peminjaman::STATUS_DIPINJAM,
                    'denda' => 0,
                ]);
            }
        }

        $peminjamans = Peminjaman::with(['anggota', 'buku'])->latest()->paginate(15);
        $permintaans = \App\Models\PermintaanPeminjaman::with(['anggota', 'buku', 'peminjaman'])->pending()->latest()->paginate(20, ['*'], 'page_permintaan');
        $title = 'Data Peminjaman';
        return view('peminjaman.index', compact('peminjamans', 'permintaans', 'title', 'setting'));
    }

    /**
     * Update the status of the specified resource (Mark as Returned).
     */
    public function returnBook(string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->isActive()) {
            $peminjaman->update([
                'status' => Peminjaman::STATUS_DIKEMBALIKAN,
                'tgl_kembali_aktual' => Carbon::now('Asia/Jakarta')
            ]);
            
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'username' => auth()->user()->username ?? 'System',
                'deskripsi' => 'Menandai buku dikembalikan. ID Pinjam: ' . $peminjaman->id
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

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Mengubah pengaturan denda dan toleransi.'
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Pengaturan denda dan toleransi berhasil diperbarui.');
    }

    public function accPermintaan(string $id)
    {
        $permintaan = \App\Models\PermintaanPeminjaman::findOrFail($id);

        if (!$permintaan->isPending()) {
            return redirect()->route('peminjaman.index')->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        if ($permintaan->isBorrowRequest()) {
            $buku = $permintaan->buku;
            $sedangDipinjam = Peminjaman::where('buku_id', $buku->id)->active()->count();
            if ($buku->stok - $sedangDipinjam <= 0) {
                return redirect()->route('peminjaman.index')->with('error', 'Stok buku ini sedang habis.');
            }

            Peminjaman::create([
                'anggota_id' => $permintaan->anggota_id,
                'buku_id' => $permintaan->buku_id,
                'tgl_pinjam' => now(),
                'tgl_kembali_rencana' => now()->addDays($permintaan->durasi),
                'status' => Peminjaman::STATUS_DIPINJAM,
                'denda' => 0
            ]);

            $permintaan->update([
                'status' => \App\Models\PermintaanPeminjaman::STATUS_APPROVED,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'username' => auth()->user()->username ?? 'System',
                'deskripsi' => 'Menerima permintaan pinjam buku ID: ' . $buku->id . ' oleh anggota ID: ' . $permintaan->anggota_id
            ]);

            return redirect()->route('peminjaman.index')->with('success', 'Permintaan peminjaman berhasil disetujui.');

        } elseif ($permintaan->isReturnRequest()) {
            $peminjaman = $permintaan->peminjaman;
            if ($peminjaman && $peminjaman->isActive()) {
                $peminjaman->update([
                    'status' => Peminjaman::STATUS_DIKEMBALIKAN,
                    'tgl_kembali_aktual' => now()
                ]);
            }

            $permintaan->update([
                'status' => \App\Models\PermintaanPeminjaman::STATUS_APPROVED,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'username' => auth()->user()->username ?? 'System',
                'deskripsi' => 'Menerima permintaan kembalikan buku ID: ' . $permintaan->buku_id . ' oleh anggota ID: ' . $permintaan->anggota_id
            ]);

            return redirect()->route('peminjaman.index')->with('success', 'Permintaan pengembalian berhasil disetujui.');
        }

        return redirect()->route('peminjaman.index')->with('error', 'Jenis permintaan tidak valid.');
    }

    public function tolakPermintaan(string $id)
    {
        $permintaan = \App\Models\PermintaanPeminjaman::findOrFail($id);

        if (!$permintaan->isPending()) {
            return redirect()->route('peminjaman.index')->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $permintaan->update([
            'status' => \App\Models\PermintaanPeminjaman::STATUS_REJECTED,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menolak permintaan ' . $permintaan->jenis . ' buku ID: ' . $permintaan->buku_id . ' oleh anggota ID: ' . $permintaan->anggota_id
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Permintaan berhasil ditolak.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'System',
            'deskripsi' => 'Menghapus data peminjaman ID: ' . $id
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
