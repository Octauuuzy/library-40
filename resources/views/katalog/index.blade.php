@extends('layouts.member')

@section('content')

    <!-- Welcome Banner Box -->
    <div class="bg-[#1e2a44] rounded-2xl p-8 flex justify-between items-center text-white shadow-xl relative overflow-hidden">
        <!-- Decorative Blur elements behind -->
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-400 opacity-20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 left-10 w-64 h-64 bg-indigo-500 opacity-20 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 pl-2">
            <h2 class="text-4xl font-extrabold mb-3 flex items-center">
                Selamat Siang, {{ explode(' ', Auth::user()->name)[0] }}! <span class="ml-3 text-3xl">🎉</span>
            </h2>
            <p class="text-blue-100/90 text-lg font-medium">Selamat datang dan semoga nyaman membaca diperpustakaan kami, ya! &gt;_&lt;</p>
        </div>
        
        <!-- Denda Box inside Banner -->
        <div class="relative z-10 bg-[#facc15] text-yellow-900 rounded-2xl px-8 py-5 flex flex-col items-center justify-center min-w-[160px] shadow-lg transform rotate-1 hover:rotate-0 transition-transform">
            <span class="text-xs font-black tracking-widest mb-1 opacity-80 uppercase">Denda</span>
            <span class="text-2xl font-black">Rp {{ number_format($denda, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="mt-10">
        <!-- Title & Subtitle -->
        <div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Rekomendasi Buku</h3>
            <p class="text-gray-500 mt-1 font-medium">Tentukan jendela dunia-mu!</p>
        </div>

        <!-- Category Capsules / Badges -->
        <div class="flex space-x-3 mt-6 overflow-x-auto pb-2 scrollbar-hide">
            <button class="px-6 py-2.5 rounded-full bg-[#1e2a44] text-white text-sm font-semibold whitespace-nowrap shadow-md hover:bg-[#2a3a5c] transition-colors">Semua</button>
            <button class="px-6 py-2.5 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold whitespace-nowrap shadow-sm transition-colors">Action</button>
            <button class="px-6 py-2.5 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold whitespace-nowrap shadow-sm transition-colors">Drama</button>
            <button class="px-6 py-2.5 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold whitespace-nowrap shadow-sm transition-colors">Fantasy</button>
            <button class="px-6 py-2.5 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold whitespace-nowrap shadow-sm transition-colors">Finance</button>
            <button class="px-6 py-2.5 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold whitespace-nowrap shadow-sm transition-colors">Psychology</button>
        </div>

        <!-- Book Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mt-8">
            @forelse($bukus as $buku)
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer flex flex-col h-full relative">
                
                <!-- Book Cover container -->
                <div class="relative aspect-[3/4] rounded-xl overflow-hidden mb-4 bg-gray-100 shadow-inner">
                    @if($buku->cover)
                        <img src="{{ asset($buku->cover) }}" alt="Cover {{ $buku->judul }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 ease-out">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-medium">No Cover</div>
                    @endif
                    
                    <!-- Category Badge -->
                    <div class="absolute top-3 right-3 bg-gray-900/80 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm">
                        {{ $buku->kategori->nama ?? 'Tanpa Kategori' }}
                    </div>
                </div>
                
                <!-- Book Info -->
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-gray-800 text-base leading-tight mb-1 line-clamp-2" title="{{ $buku->judul }}">{{ $buku->judul }}</h4>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <p class="text-xs font-medium text-gray-500 truncate pr-2">{{ $buku->pengarang }}</p>
                        <div class="flex items-center text-yellow-500 text-xs font-bold bg-yellow-50 px-1.5 py-0.5 rounded">
                            <svg class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.898 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            0
                        </div>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p class="text-gray-500 font-medium text-lg">Belum ada koleksi buku yang tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>

@endsection