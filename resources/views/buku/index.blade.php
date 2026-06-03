@extends('layouts.admin')

@section('content')
    <div class="py-4" x-data="{ 
        addModalOpen: false,
        editModalOpen: false, 
        deleteModalOpen: false,
        editForm: {
            id: '',
            kategori_id: '',
            judul: '',
            pengarang: '',
            tahun_terbit: '',
            stok: '',
            sinopsis: ''
        },
        deleteForm: {
            id: ''
        },
        openEditModal(buku) {
            this.editForm.id = buku.id;
            this.editForm.kategori_id = buku.kategoris && buku.kategoris.length > 0 ? buku.kategoris[0].id : '';
            this.editForm.judul = buku.judul;
            this.editForm.pengarang = buku.pengarang;
            this.editForm.tahun_terbit = buku.tahun_terbit;
            this.editForm.stok = buku.stok;
            this.editForm.sinopsis = buku.sinopsis;
            this.editModalOpen = true;
        },
        openDeleteModal(id) {
            this.deleteForm.id = id;
            this.deleteModalOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-3xl font-bold text-[#1e293b]">Data Buku</h2>
                        <div class="flex space-x-3">
                            <button type="button" @click="addModalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white font-medium w-10 h-10 rounded-md transition-colors shadow-sm flex items-center justify-center text-2xl pb-1">
                                +
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-800 text-sm leading-normal font-bold">
                                    <th class="py-3 px-4 text-left border-b">ID Buku</th>
                                    <th class="py-3 px-4 text-center border-b">Cover</th>
                                    <th class="py-3 px-4 text-left border-b">Judul</th>
                                    <th class="py-3 px-4 text-left border-b">Penulis</th>
                                    <th class="py-3 px-4 text-left border-b">Kategori</th>
                                    <th class="py-3 px-4 text-center border-b">Tahun</th>
                                    <th class="py-3 px-4 text-center border-b">Stok</th>
                                    <th class="py-3 px-4 text-center border-b">Sisa Stok</th>
                                    <th class="py-3 px-4 text-center border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($bukus as $buku)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-left"><span class="font-medium text-gray-900">{{ str_pad($buku->id, 7, '0', STR_PAD_LEFT) }}</span></td>
                                        <td class="py-3 px-4 text-center flex justify-center">
                                            @if($buku->cover)
                                                <img src="{{ asset($buku->cover) }}" alt="Cover {{ $buku->judul }}" class="h-16 w-12 object-cover rounded shadow-sm border border-gray-200">
                                            @else
                                                <div class="h-16 w-12 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs border border-gray-300">
                                                    No Cover
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-left text-gray-800 font-medium">{{ $buku->judul }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $buku->pengarang }}</td>
                                        <td class="py-3 px-4 text-left">
                                            <span class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-medium border border-gray-200">
                                                {{ $buku->kategoris->first()->nama_kategori ?? 'Tanpa Kategori' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center text-gray-800">{{ $buku->tahun_terbit }}</td>
                                        <td class="py-3 px-4 text-center text-gray-800 font-semibold">{{ $buku->stok }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @php
                                                $sisaStok = $buku->stok - $buku->dipinjam_count;
                                            @endphp
                                            <span class="{{ $sisaStok > 0 ? 'text-green-600' : 'text-red-500' }} font-bold">
                                                {{ $sisaStok }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <button type="button" @click="openEditModal({{ $buku }})" class="bg-amber-400 hover:bg-amber-500 text-white p-1.5 rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button type="button" @click="openDeleteModal({{ $buku->id }})" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($bukus->isEmpty())
                                    <tr>
                                        <td colspan="9" class="py-8 text-center text-gray-500">Belum ada data buku.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if ($bukus->hasPages())
                        <div class="mt-4 flex justify-center items-center space-x-2">
                            @if ($bukus->onFirstPage())
                                <span class="bg-gray-200 text-gray-400 px-3 py-1 rounded cursor-not-allowed font-bold">&lt;</span>
                            @else
                                <a href="{{ $bukus->previousPageUrl() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded transition-colors font-bold">&lt;</a>
                            @endif
                            
                            <span class="text-gray-700 font-medium px-3">{{ $bukus->currentPage() }}</span>
                            
                            @if ($bukus->hasMorePages())
                                <a href="{{ $bukus->nextPageUrl() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded transition-colors font-bold">&gt;</a>
                            @else
                                <span class="bg-gray-200 text-gray-400 px-3 py-1 rounded cursor-not-allowed font-bold">&gt;</span>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Add Modal Popup -->
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="addModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <form action="{{ route('buku.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 border-b pb-3" id="modal-title">
                                Tambah Data Buku
                            </h3>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="add_stok" class="block text-sm font-medium text-gray-700 mb-1">Total Stok</label>
                                    <input type="number" name="stok" id="add_stok" min="0" step="1" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                
                                <div>
                                    <label for="add_pengarang" class="block text-sm font-medium text-gray-700 mb-1">Penulis / Pengarang</label>
                                    <input type="text" name="pengarang" id="add_pengarang" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div>
                                    <label for="add_kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <select name="kategori_id" id="add_kategori_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="add_tahun_terbit" class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit" id="add_tahun_terbit" placeholder="YYYY" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="add_stok" class="block text-sm font-medium text-gray-700 mb-1">Total Stok</label>
                                    <input type="number" name="stok" id="add_stok" min="0" step="1" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="add_cover" class="block text-sm font-medium text-gray-700 mb-1">Upload Cover Buku</label>
                                    <input type="file" name="cover" id="add_cover" accept="image/*" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF (Maks. 2MB)</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end space-x-3 rounded-b-2xl">
                            <button type="button" @click="addModalOpen = false" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                Batalkan
                            </button>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal Popup -->
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="editModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <form :action="`/buku/${editForm.id}`" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 border-b pb-3" id="modal-title">
                                Edit Data Buku
                            </h3>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="edit_judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
                                    <input type="text" name="judul" id="edit_judul" x-model="editForm.judul" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                
                                <div>
                                    <label for="edit_pengarang" class="block text-sm font-medium text-gray-700 mb-1">Penulis / Pengarang</label>
                                    <input type="text" name="pengarang" id="edit_pengarang" x-model="editForm.pengarang" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div>
                                    <label for="edit_kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <select name="kategori_id" id="edit_kategori_id" x-model="editForm.kategori_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                        <option value="" disabled>Pilih Kategori</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_tahun_terbit" class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit" id="edit_tahun_terbit" x-model="editForm.tahun_terbit" placeholder="YYYY" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="add_stok" class="block text-sm font-medium text-gray-700 mb-1">Total Stok</label>
                                    <input type="number" name="stok" id="add_stok" min="0" step="1" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="edit_sinopsis" class="block text-sm font-medium text-gray-700 mb-1">Sinopsis / Deskripsi (Opsional)</label>
                                    <textarea name="sinopsis" id="edit_sinopsis" rows="4" x-model="editForm.sinopsis" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="edit_cover" class="block text-sm font-medium text-gray-700 mb-1">Update Cover Buku (Opsional)</label>
                                    <input type="file" name="cover" id="edit_cover" accept="image/*" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah cover. Format: JPG, PNG, GIF (Maks. 2MB)</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end space-x-3 rounded-b-2xl">
                            <button type="button" @click="editModalOpen = false" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                Batalkan
                            </button>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal Popup -->
        <div x-show="deleteModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="deleteModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form :action="`/buku/${deleteForm.id}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="bg-white px-4 pt-8 pb-6 sm:p-6 sm:pb-6 text-center">
                            <div class="flex justify-center mb-6">
                                <div class="h-24 w-24 rounded-full border-4 border-red-500 flex items-center justify-center">
                                    <span class="text-red-600 text-6xl font-bold">!</span>
                                </div>
                            </div>
                            <div class="mt-2 mb-2">
                                <h3 class="text-2xl leading-6 font-bold text-gray-900" id="modal-title">
                                    Yakin ingin menghapus buku ini?
                                </h3>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-center space-x-4 rounded-b-2xl">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                Lanjutkan
                            </button>
                            <button type="button" @click="deleteModalOpen = false" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsectionv>
        </div>

    </div>
@endsection