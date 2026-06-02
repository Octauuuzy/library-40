<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberReturnBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_return_their_active_book_from_katalog(): void
    {
        $anggota = Anggota::create([
            'nis' => 'NIS001',
            'nama' => 'Anggota Satu',
            'kelas' => 'X-A',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Perpustakaan',
        ]);

        $user = User::factory()->create([
            'anggota_id' => $anggota->id,
        ]);

        $buku = Buku::create([
            'judul' => 'Laskar Awan',
            'pengarang' => 'Andrea Kirata',
            'tahun_terbit' => 2024,
            'stok' => 3,
        ]);

        $peminjaman = Peminjaman::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_rencana' => now()->addDays(7),
            'status' => 'Dipinjam',
            'denda' => 0,
        ]);

        $this->assertSame(Peminjaman::STATUS_DIPINJAM, $peminjaman->fresh()->status_normalized);

        $response = $this->actingAs($user)->postJson(route('katalog.kembalikan'), [
            'peminjaman_id' => $peminjaman->id,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'Dikembalikan',
            ]);

        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => Peminjaman::STATUS_DIKEMBALIKAN,
        ]);

        $this->assertNotNull(Peminjaman::findOrFail($peminjaman->id)->tgl_kembali_aktual);
    }

    public function test_member_cannot_return_another_members_book(): void
    {
        $ownerAnggota = Anggota::create([
            'nis' => 'NIS002',
            'nama' => 'Pemilik Buku',
            'kelas' => 'X-B',
            'no_hp' => '081111111111',
            'alamat' => 'Jl. Pemilik',
        ]);

        $otherAnggota = Anggota::create([
            'nis' => 'NIS003',
            'nama' => 'User Lain',
            'kelas' => 'X-C',
            'no_hp' => '082222222222',
            'alamat' => 'Jl. Lain',
        ]);

        $owner = User::factory()->create([
            'anggota_id' => $ownerAnggota->id,
        ]);

        $otherUser = User::factory()->create([
            'anggota_id' => $otherAnggota->id,
        ]);

        $buku = Buku::create([
            'judul' => 'Buku Rahasia',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2023,
            'stok' => 1,
        ]);

        $peminjaman = Peminjaman::create([
            'anggota_id' => $owner->anggota_id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_rencana' => now()->addDays(7),
            'status' => Peminjaman::STATUS_DIPINJAM,
            'denda' => 0,
        ]);

        $this->actingAs($otherUser)
            ->postJson(route('katalog.kembalikan'), [
                'peminjaman_id' => $peminjaman->id,
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => Peminjaman::STATUS_DIPINJAM,
        ]);
    }
}
