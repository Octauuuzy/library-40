@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <p class="text-gray-500 text-sm">Selamat datang, Administrator!</p>
</div>

<!-- Stats Top -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Buku -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-14 h-14 rounded-lg bg-blue-50 flex items-center justify-center mr-4">
            <svg class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalBuku }}</h3>
            <p class="text-sm text-gray-500 font-medium">Total Buku</p>
        </div>
    </div>

    <!-- Total Anggota -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-14 h-14 rounded-lg bg-green-50 flex items-center justify-center mr-4">
            <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalAnggota }}</h3>
            <p class="text-sm text-gray-500 font-medium">Total Anggota</p>
        </div>
    </div>

    <!-- Sedang Dipinjam -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-14 h-14 rounded-lg bg-yellow-50 flex items-center justify-center mr-4">
            <svg class="h-8 w-8 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $sedangDipinjam }}</h3>
            <p class="text-sm text-gray-500 font-medium">Sedang Dipinjam</p>
        </div>
    </div>

    <!-- Kategori -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-14 h-14 rounded-lg bg-teal-50 flex items-center justify-center mr-4">
            <svg class="h-8 w-8 text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalKategori }}</h3>
            <p class="text-sm text-gray-500 font-medium">Kategori</p>
        </div>
    </div>
</div>

<!-- Stats Middle -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
        <h4 class="text-gray-500 font-semibold mb-2">Total Peminjaman</h4>
        <p class="text-4xl font-bold text-blue-600">{{ $totalPeminjaman }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
        <h4 class="text-gray-500 font-semibold mb-2">Sedang Dipinjam</h4>
        <p class="text-4xl font-bold text-yellow-500">{{ $sedangDipinjam }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
        <h4 class="text-gray-500 font-semibold mb-2">Dikembalikan</h4>
        <p class="text-4xl font-bold text-green-500">{{ $dikembalikan }}</p>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Table Peminjaman Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div class="flex items-center text-gray-800 font-bold text-lg">
            <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Peminjaman Terbaru
        </div>
        <a href="{{ route('peminjaman.index') }}" class="text-sm text-blue-600 border border-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-md font-medium transition-colors">
            Lihat Semua
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 bg-white">
                    <th class="px-6 py-3 text-sm font-semibold text-gray-800">Peminjam</th>
                    <th class="px-6 py-3 text-sm font-semibold text-gray-800">Judul Buku</th>
                    <th class="px-6 py-3 text-sm font-semibold text-gray-800">Tgl Pinjam</th>
                    <th class="px-6 py-3 text-sm font-semibold text-gray-800">Tgl Kembali</th>
                    <th class="px-6 py-3 text-sm font-semibold text-gray-800">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($peminjamanTerbaru as $peminjaman)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $peminjaman->user->name ?? 'User Terhapus' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $peminjaman->buku->judul ?? 'Buku Terhapus' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $peminjaman->tanggal_kembali->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($peminjaman->status == 'Dipinjam')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                {{ $peminjaman->status }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                {{ $peminjaman->status }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                        Belum ada data peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <!-- Buku Populer -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center text-gray-800 font-bold text-lg">
                <svg class="h-5 w-5 mr-2 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Buku Populer
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-white">
                        <th class="px-6 py-3 text-sm font-semibold text-gray-800">Judul Buku</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-800 text-center">Total Dipinjam</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bukuPopuler as $buku)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-700 font-medium truncate max-w-[150px]">{{ $buku->judul }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $buku->peminjamans_count }} kali
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500">
                            Data belum mencukupi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection