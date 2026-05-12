<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@perpustakaan.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Member User 1
        $user1 = User::create([
            'name' => 'Octaa',
            'username' => 'octaa',
            'email' => 'octa@projectocta.me',
            'password' => Hash::make('password'),
            'role' => 'anggota',
        ]);

        // Create Anggota 1 with matching ID
        Anggota::create([
            'id' => $user1->id,
            'nis' => '0000003',
            'nama' => $user1->name,
            'kelas' => 'XI RPL 1',
            'no_hp' => '911',
            'alamat' => '-',
        ]);

        // Create Member User 2
        $user2 = User::create([
            'name' => 'Prabowo Subinato',
            'username' => 'prabowosubinato.official',
            'email' => 'nato.subinato@nato.gov',
            'password' => Hash::make('password'),
            'role' => 'anggota',
        ]);

        // Create Anggota 2
        Anggota::create([
            'id' => $user2->id,
            'nis' => '0000002',
            'nama' => $user2->name,
            'kelas' => 'XI RPL 2',
            'no_hp' => '911',
            'alamat' => 'Jl. Kemayoran',
        ]);
        
        // Add Kategori
        $katAction = \App\Models\Kategori::create(['nama_kategori' => 'Action']);
        $katDrama = \App\Models\Kategori::create(['nama_kategori' => 'Drama']);
        $katPsychology = \App\Models\Kategori::create(['nama_kategori' => 'Psychology']);
        $katFinance = \App\Models\Kategori::create(['nama_kategori' => 'Finance']);

        // Add Buku
        $buku1 = \App\Models\Buku::create([
            'judul' => 'Laskar Awan',
            'pengarang' => 'Andrea Kirata',
            'tahun_terbit' => 2005,
            'stok' => 10,
        ]);
        $buku1->kategoris()->attach([$katDrama->id]);

        $buku2 = \App\Models\Buku::create([
            'judul' => 'The Psychology of Money',
            'pengarang' => 'Morgan Housel',
            'tahun_terbit' => 2020,
            'stok' => 5,
        ]);
        $buku2->kategoris()->attach([$katPsychology->id]);

        $buku3 = \App\Models\Buku::create([
            'judul' => 'The Laws of Human Nature',
            'pengarang' => 'Robert Greene',
            'tahun_terbit' => 2018,
            'stok' => 3,
        ]);
        $buku3->kategoris()->attach([$katPsychology->id]);

        $buku4 = \App\Models\Buku::create([
            'judul' => 'The Uncomfortable Truth About Money',
            'pengarang' => 'Paul Podolsky',
            'tahun_terbit' => 2021,
            'stok' => 7,
        ]);
        $buku4->kategoris()->attach([$katFinance->id]);
        
        $buku5 = \App\Models\Buku::create([
            'judul' => 'Think and Grow Rich',
            'pengarang' => 'Napoleon Hill',
            'tahun_terbit' => 1937,
            'stok' => 12,
        ]);
        $buku5->kategoris()->attach([$katFinance->id]);

        $buku6 = \App\Models\Buku::create([
            'judul' => 'The Walking Dead',
            'pengarang' => 'Robert Kirkman',
            'tahun_terbit' => 2003,
            'stok' => 8,
        ]);
        $buku6->kategoris()->attach([$katAction->id]);

        // Setting
        \App\Models\Setting::create([
            'toleransi_hari' => 1,
            'denda_per_hari' => 5000,
        ]);
    }
}