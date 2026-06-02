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
        borrowModalOpen: false,
        returnModalOpen: false,
        borrowDuration: 7,
        selectedBook: null,
        isSubmitting: false,
        openModal(detail) {
            this.selectedBook = {
                ...detail.buku,
                kategori_name: detail.kategori,
                cover_url: detail.cover
            };
            this.modalOpen = true;
        },
        openBorrowModal() {
            if (!this.selectedBook) return;
            this.modalOpen = false;
            this.borrowModalOpen = true;
        },
        openReturnModal() {
            if (!this.selectedBook || !this.selectedBook.active_loan_id) return;
            this.modalOpen = false;
            this.returnModalOpen = true;
        },
        handlePrimaryAction() {
            if (this.selectedBook && this.selectedBook.is_borrowed_by_user) {
                this.openReturnModal();
                return;
            }

            this.openBorrowModal();
        },
        submitBorrow() {
            if (this.isSubmitting || !this.selectedBook) return;

            const csrfToken = document.querySelector('meta[name=\'csrf-token\']');
            if (!csrfToken) return alert('CSRF token missing');

            this.isSubmitting = true;

            fetch('/katalog/pinjam', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    buku_id: this.selectedBook.id,
                    durasi: this.borrowDuration
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.borrowModalOpen = false;
                    window.location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan sistem.');
                console.error(error);
            })
            .finally(() => {
                this.isSubmitting = false;
            });
        },
        submitReturn() {
            if (this.isSubmitting || !this.selectedBook || !this.selectedBook.active_loan_id) return;

            const csrfToken = document.querySelector('meta[name=\'csrf-token\']');
            if (!csrfToken) return alert('CSRF token missing');

            this.isSubmitting = true;

            fetch('/katalog/kembalikan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    peminjaman_id: this.selectedBook.active_loan_id
                })
            })
            .then(async response => {
                const data = await response.json();
                return { ok: response.ok, data };
            })
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    alert(data.message);
                    this.returnModalOpen = false;
                    window.location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan sistem.');
                console.error(error);
            })
            .finally(() => {
                this.isSubmitting = false;
            });
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
                    buku: {{ json_encode(['id' => $buku->id, 'judul' => $buku->judul, 'pengarang' => $buku->pengarang, 'sinopsis' => $buku->sinopsis, 'favorits_count' => $buku->favorits_count, 'is_favorited' => $buku->is_favorited, 'is_borrowed_by_user' => (bool) ($buku->is_borrowed_by_user ?? false), 'active_loan_id' => optional($activePeminjamanByBook->get($buku->id))->id]) }},
                    kategori: {{ json_encode($buku->kategoris->first()->nama_kategori ?? 'Tanpa Kategori') }},
                    cover: {{ json_encode($buku->cover ? asset($buku->cover) : '') }}
                 }"
                 @favorit-updated.window="if($event.detail.buku_id == buku.id) { buku = { ...buku, favorits_count: $event.detail.count, is_favorited: $event.detail.is_favorited }; }"
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
                        <div class="flex items-center text-yellow-500 text-xs font-bold bg-yellow-50 px-1.5 py-0.5 rounded cursor-pointer z-20" @click.stop.prevent="window.toggleFavorit(buku.id, $event)">
                            <svg x-show="buku.is_favorited" class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.898 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg x-show="!buku.is_favorited" style="display: none;" class="w-3.5 h-3.5 mr-0.5 text-gray-400 hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"></path></svg>
                            <span x-text="buku.favorits_count"></span>
                        </div>
                    </div>
                </div>

            </div>            @empty
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
                                <button @click="handlePrimaryAction()" :class="selectedBook && selectedBook.is_borrowed_by_user ? 'bg-red-600 hover:bg-red-700' : 'bg-[#0b1221] hover:bg-[#1e2a44]'" class="w-full sm:w-auto text-white font-bold py-3.5 px-8 rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                                    <template x-if="selectedBook && selectedBook.is_borrowed_by_user">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5v10A2.5 2.5 0 0117.5 20h-11A2.5 2.5 0 014 17.5v-10z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h10m0 0l-3-3m3 3l-3 3" />
                                        </svg>
                                    </template>
                                    <template x-if="!selectedBook || !selectedBook.is_borrowed_by_user">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </template>
                                    <span x-text="selectedBook && selectedBook.is_borrowed_by_user ? 'Kembalikan Buku' : 'Pinjam Buku'"></span>
                                </button>
                                
                                <!-- Star Action -->
                                <div class="mt-4 flex items-center text-gray-500 text-sm font-medium">
                                    <button class="transition-colors flex items-center mr-2 focus:outline-none" @click.stop.prevent="window.toggleFavorit(selectedBook.id, $event)">
                                        <svg x-show="selectedBook && selectedBook.is_favorited" class="h-6 w-6 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.898 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <svg x-show="selectedBook && !selectedBook.is_favorited" class="h-6 w-6 mr-1 text-gray-400 hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"></path></svg>
                                    </button>
                                    <span><span x-text="selectedBook ? selectedBook.favorits_count : 0"></span> orang menyukai</span>
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

        <!-- Borrow Modal -->
        <div x-show="borrowModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="borrowModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="borrowModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="borrowModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    
                    <div class="p-8">
                        <div class="flex justify-center mb-4">
                            <div class="text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 3.5c-2.5 0-4.5.5-7 1.5v14c2.5-1 4.5-1.5 7-1.5s4.5.5 7 1.5v-14c-2.5-1-4.5-1.5-7-1.5zm-1 12.8c-1.5.5-3.5 1-6 1.8v-11.8c2.5-1 4.5-1.5 6-1.5v11.5zm8 1.8c-2.5-.8-4.5-1.3-6-1.8v-11.5c1.5.5 3.5 1 6 1.5v11.8z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-extrabold text-center text-gray-900 mb-6">Pinjam Buku</h3>
                        
                        <div class="bg-gray-50 rounded-2xl p-4 flex mb-6 border border-gray-100">
                            <div class="w-16 h-24 bg-gray-200 rounded-lg flex-shrink-0 overflow-hidden shadow-sm mr-4">
                                <template x-if="selectedBook && selectedBook.cover_url">
                                    <img :src="selectedBook.cover_url" :alt="'Cover ' + selectedBook.judul" class="w-full h-full object-cover">
                                </template>
                            </div>
                            <div class="flex flex-col justify-center">
                                <h4 class="font-bold text-gray-900 text-base leading-tight mb-1" x-text="selectedBook ? selectedBook.judul : ''"></h4>
                                <p class="text-sm text-gray-500 mb-2" x-text="selectedBook ? selectedBook.pengarang : ''"></p>
                                <div>
                                    <span class="inline-block bg-[#1e2a44] text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-sm" x-text="selectedBook ? selectedBook.kategori_name : ''"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-800 mb-2">Durasi Peminjaman</label>
                            <select x-model="borrowDuration" class="w-full bg-white border border-gray-300 text-gray-900 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3 px-4 shadow-sm appearance-none cursor-pointer">
                                <option value="7">7 hari</option>
                                <option value="14">14 hari</option>
                                <option value="30">30 hari</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Maksimal peminjaman 30 hari</p>
                        </div>

                        <div class="flex space-x-3 justify-center">
                            <button @click="borrowModalOpen = false" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-full shadow-sm transition-colors w-1/2">Batalkan</button>
                            <button @click="submitBorrow()" :disabled="isSubmitting" class="px-8 py-3 bg-[#0b1221] hover:bg-[#1e2a44] disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold rounded-full shadow-md transition-colors w-1/2">Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Modal -->
        <div x-show="returnModalOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="return-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="returnModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="returnModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="returnModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-[28px] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                    <div class="px-7 pt-8 pb-7 sm:px-8">
                        <div class="flex justify-center mb-7">
                            <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-1.998 4.043-1.998 5.198 0l7.354 12.72c1.154 1.998-.289 4.497-2.599 4.497H4.646c-2.31 0-3.753-2.5-2.599-4.498L9.4 3.003zM12 8.25a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <h3 id="return-modal-title" class="text-3xl font-extrabold text-center text-gray-900 mb-6">Kembalikan Buku?</h3>

                        <div class="bg-gray-50 rounded-2xl p-5 flex items-center gap-4 mb-8 border border-gray-100">
                            <div class="w-20 h-28 bg-gray-200 rounded-xl flex-shrink-0 overflow-hidden shadow-sm">
                                <template x-if="selectedBook && selectedBook.cover_url">
                                    <img :src="selectedBook.cover_url" :alt="'Cover ' + selectedBook.judul" class="w-full h-full object-cover">
                                </template>
                                <template x-if="selectedBook && !selectedBook.cover_url">
                                    <div class="w-full h-full flex items-center justify-center text-xs font-semibold text-gray-500">No Cover</div>
                                </template>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-2xl font-bold text-gray-900 leading-tight mb-2" x-text="selectedBook ? selectedBook.judul : ''"></h4>
                                <p class="text-xl text-gray-500 mb-3" x-text="selectedBook ? selectedBook.pengarang : ''"></p>
                                <span class="inline-flex bg-[#37355a] text-white text-xs font-bold px-3 py-1 rounded-full" x-text="selectedBook ? selectedBook.kategori_name : ''"></span>
                            </div>
                        </div>

                        <p class="text-center text-2xl text-gray-500 mb-8">Yakin ingin mengembalikan buku ini?</p>

                        <div class="flex justify-center gap-3">
                            <button @click="returnModalOpen = false" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-full shadow-sm transition-colors min-w-36">Batalkan</button>
                            <button @click="submitReturn()" :disabled="isSubmitting" class="px-8 py-3 bg-red-600 hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold rounded-full shadow-md transition-colors min-w-36">Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
