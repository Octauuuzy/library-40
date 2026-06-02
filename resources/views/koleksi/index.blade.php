@extends('layouts.member')

@section('content')

    <!-- Main Content Area -->
    <div class="mt-4">
        <!-- Title & Subtitle -->
        <div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Koleksi Buku Anda</h3>
            <p class="text-gray-500 mt-1 font-medium">Buku-buku yang pernah atau sedang Anda pinjam.</p>
        </div>

        <!-- Collection Filters -->
        <div class="flex flex-wrap justify-between items-center mt-6 gap-4">
            <!-- Left: Type Filter -->
            <div class="flex space-x-1 bg-gray-100 p-1.5 rounded-xl border border-gray-200/60">
                <button class="px-5 py-2 text-sm font-bold rounded-lg bg-white text-[#1e2a44] shadow-sm">Semua Buku</button>
                <button class="px-5 py-2 text-sm font-semibold rounded-lg text-gray-500 hover:text-gray-800 transition-colors">E-Book</button>
            </div>

            <!-- Right: Status Filter Dropdown -->
            <div class="relative" x-data="{ statusOpen: false }" @mouseenter="statusOpen = true" @mouseleave="statusOpen = false">
                <button class="px-5 py-2.5 text-sm font-bold rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    {{ $statusFilter == 'dipinjam' ? 'Sedang Dipinjam' : ($statusFilter == 'dikembalikan' ? 'Dikembalikan' : 'Semua Status') }}
                    <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="statusOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl z-20 py-2" style="display: none;">
                    <a href="{{ route('koleksi') }}" class="block px-4 py-2.5 text-sm font-medium {{ !$statusFilter ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }} transition-colors">Semua Status</a>
                    <a href="{{ route('koleksi', ['status' => 'dipinjam']) }}" class="block px-4 py-2.5 text-sm font-medium {{ $statusFilter == 'dipinjam' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-yellow-50' }} transition-colors">Sedang Dipinjam</a>
                    <a href="{{ route('koleksi', ['status' => 'dikembalikan']) }}" class="block px-4 py-2.5 text-sm font-medium {{ $statusFilter == 'dikembalikan' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-green-50' }} transition-colors">Dikembalikan</a>
                </div>
            </div>
        </div>

        <!-- Book Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mt-8">
            @forelse($peminjamans as $peminjaman)
            @php $buku = $peminjaman->buku; @endphp
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer flex flex-col h-full relative">
                
                <!-- Book Cover container -->
                <div class="relative aspect-[3/4] rounded-xl overflow-hidden mb-4 bg-gray-100 shadow-inner">
                    @if($buku->cover)
                        <img src="{{ asset($buku->cover) }}" alt="Cover {{ $buku->judul }}" class="w-full h-full object-cover transition-transform duration-500 ease-out">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-medium">No Cover</div>
                    @endif
                    
                    <!-- Category Badge -->
                    <div class="absolute top-3 right-3 bg-gray-900/80 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm">
                        {{ $buku->kategoris->first()->nama_kategori ?? 'Tanpa Kategori' }}
                    </div>
                    
                    <!-- Status Badge -->
                    @if($peminjaman->isReturned())
                        <div class="absolute bottom-3 left-3 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm">Dikembalikan</div>
                    @else
                        <div class="absolute bottom-3 left-3 bg-yellow-500 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm">Dipinjam</div>
                    @endif
                </div>
                
                <!-- Book Info -->
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800 text-base leading-tight mb-1 line-clamp-2" title="{{ $buku->judul }}">{{ $buku->judul }}</h4>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <p class="text-xs font-medium text-gray-500 truncate pr-2">{{ $buku->pengarang }}</p>
                        <div class="flex items-center text-blue-500 text-xs font-bold bg-blue-50 px-2 py-1 rounded">
                            Baca Buku
                        </div>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p class="text-gray-500 font-medium text-lg">Anda belum meminjam buku apapun.</p>
            </div>
            @endforelse
        </div>
    </div>

@endsection
