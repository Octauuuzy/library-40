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
    <div class="mt-10" x-data="{
        modalOpen: false,
        selectedBook: null,
        openModal(detail) {
            this.selectedBook = {
                ...detail.buku,
                kategori_name: detail.kategori,
                cover_url: detail.cover
            };
            this.modalOpen = true;
        }
    }" @open-book-modal.window="openModal($event.detail)">
        <!-- Title & Subtitle -->
        <div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Rekomendasi Buku</h3>
            <p class="text-gray-500 mt-1 font-medium">Tentukan jendela dunia-mu!</p>
        </div>

        <!-- Category Capsules / Badges -->
        <div class="flex space-x-3 mt-6 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('katalog') }}" class="px-6 py-2.5 rounded-full {{ !$selectedKategori ? 'bg-[#1e2a44] text-white shadow-md hover:bg-[#2a3a5c]' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 shadow-sm' }} text-sm font-semibold whitespace-nowrap transition-colors inline-block">Semua</a>
            @foreach($kategoris as $kategori)
            <a href="{{ route('katalog', ['kategori' => $kategori->id]) }}" class="px-6 py-2.5 rounded-full {{ $selectedKategori == $kategori->id ? 'bg-[#1e2a44] text-white shadow-md hover:bg-[#2a3a5c]' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 shadow-sm' }} text-sm font-semibold whitespace-nowrap transition-colors inline-block">{{ $kategori->nama_kategori }}</a>
            @endforeach
        </div>

        <!-- Book Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mt-8">
            @forelse($bukus as $buku)
            <div x-data="{
                    buku: {{ json_encode(['id' => $buku->id, 'judul' => $buku->judul, 'pengarang' => $buku->pengarang, 'sinopsis' => $buku->sinopsis]) }},
                    kategori: {{ json_encode($buku->kategoris->first()->nama_kategori ?? 'Tanpa Kategori') }},
                    cover: {{ json_encode($buku->cover ? asset($buku->cover) : '') }}
                 }"
                 @click="$dispatch('open-book-modal', { buku: buku, kategori: kategori, cover: cover })"
                 class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 group cursor-pointer flex flex-col h-full relative">
                
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

        <!-- Book Detail Modal -->
        <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="modalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                    
                    <!-- Close button -->
                    <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="bg-white">
                        <!-- Top Half: Cover and Info -->
                        <div class="flex flex-col md:flex-row p-8 border-b border-gray-100">
                            <!-- Cover -->
                            <div class="w-full md:w-1/3 flex justify-center mb-6 md:mb-0">
                                <template x-if="selectedBook && selectedBook.cover_url">
                                    <img :src="selectedBook.cover_url" :alt="'Cover ' + selectedBook.judul" class="w-48 h-72 object-cover rounded-xl shadow-lg border border-gray-200">
                                </template>
                                <template x-if="selectedBook && !selectedBook.cover_url">
                                    <div class="w-48 h-72 bg-gray-200 rounded-xl flex items-center justify-center text-gray-500 font-medium border border-gray-300 shadow-md">
                                        No Cover
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Info -->
                            <div class="w-full md:w-2/3 md:pl-8 flex flex-col justify-center relative">
                                <h3 class="text-3xl font-extrabold text-gray-900 mb-2 leading-tight pr-8" x-text="selectedBook ? selectedBook.judul : ''"></h3>
                                <p class="text-lg text-gray-600 mb-4 font-medium" x-text="selectedBook ? selectedBook.pengarang : ''"></p>
                                
                                <div class="mb-6">
                                    <span class="inline-block bg-[#1e2a44] text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm tracking-wide" x-text="selectedBook ? selectedBook.kategori_name : ''"></span>
                                </div>
                                
                                <!-- Borrow Button -->
                                <button class="w-full sm:w-auto bg-[#0b1221] hover:bg-[#1e2a44] text-white font-bold py-3.5 px-8 rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span>Pinjam Buku</span>
                                </button>
                                
                                <!-- Star Action -->
                                <div class="mt-4 flex items-center text-gray-500 text-sm font-medium">
                                    <button class="hover:text-yellow-500 transition-colors flex items-center mr-2 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.898 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                    <span>0 orang menyukai</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Half: Synopsis -->
                        <div class="p-8 bg-white">
                            <h4 class="text-lg font-bold text-gray-900 mb-3">Sinopsis:</h4>
                            <p class="text-gray-600 leading-relaxed text-sm text-justify whitespace-pre-line" x-text="selectedBook && selectedBook.sinopsis ? selectedBook.sinopsis : 'Sinopsis belum tersedia untuk buku ini. Silakan hubungi pustakawan untuk informasi lebih lanjut.'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection