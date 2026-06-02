@extends('layouts.admin')

@section('content')
    <div class="py-4" x-data="{ 
        settingModalOpen: false, 
        deleteModalOpen: false,
        returnModalOpen: false,
        settingForm: {
            toleransi_hari: {{ $setting->toleransi_hari }},
            denda_per_hari: {{ $setting->denda_per_hari }}
        },
        deleteForm: {
            id: ''
        },
        returnForm: {
            id: ''
        },
        openDeleteModal(id) {
            this.deleteForm.id = id;
            this.deleteModalOpen = true;
        },
        openReturnModal(id) {
            this.returnForm.id = id;
            this.returnModalOpen = true;
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

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
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
                        <h2 class="text-3xl font-bold text-[#1e293b]">Data Peminjaman</h2>
                        <div class="flex space-x-3">
                            <button type="button" @click="settingModalOpen = true" class="bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 font-medium p-2 rounded-md transition-colors shadow-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-800 text-sm leading-normal font-bold">
                                    <th class="py-3 px-4 text-left border-b">No</th>
                                    <th class="py-3 px-4 text-left border-b">Peminjam</th>
                                    <th class="py-3 px-4 text-left border-b">Judul Buku</th>
                                    <th class="py-3 px-4 text-center border-b">Tgl Pinjam</th>
                                    <th class="py-3 px-4 text-center border-b">Batas Kembali</th>
                                    <th class="py-3 px-4 text-center border-b">Tgl Dikembalikan</th>
                                    <th class="py-3 px-4 text-right border-b">Denda</th>
                                    <th class="py-3 px-4 text-center border-b">Status</th>
                                    <th class="py-3 px-4 text-center border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($peminjamans as $key => $pinjam)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-left whitespace-nowrap">{{ $key + 1 }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800 font-medium">{{ $pinjam->anggota->nama ?? 'Anggota Terhapus' }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $pinjam->buku->judul ?? 'Buku Terhapus' }}</td>
                                        <td class="py-3 px-4 text-center">{{ $pinjam->tgl_pinjam->format('d/m/Y') }}</td>
                                        <td class="py-3 px-4 text-center">{{ $pinjam->tgl_kembali_rencana->format('d/m/Y') }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($pinjam->tgl_kembali_aktual)
                                                {{ $pinjam->tgl_kembali_aktual->format('d/m/Y') }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right text-red-500 font-semibold">
                                            Rp {{ number_format($pinjam->denda, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($pinjam->isBorrowed())
                                                <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold border border-yellow-200">Dipinjam</span>
                                            @elseif($pinjam->isLate())
                                                <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs font-semibold border border-red-200">Terlambat</span>
                                            @else
                                                <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold border border-green-200">Dikembalikan</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                @if($pinjam->isActive())
                                                    <button type="button" @click="openReturnModal({{ $pinjam->id }})" class="bg-green-500 hover:bg-green-600 text-white p-1.5 rounded-md transition-colors" title="Tandai Dikembalikan">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button type="button" @click="openDeleteModal({{ $pinjam->id }})" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($peminjamans->isEmpty())
                                    <tr>
                                        <td colspan="9" class="py-8 text-center text-gray-500">Belum ada data peminjaman.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if ($peminjamans->hasPages())
                        <div class="mt-4 flex justify-center items-center space-x-2">
                            @if ($peminjamans->onFirstPage())
                                <span class="bg-gray-200 text-gray-400 px-3 py-1 rounded cursor-not-allowed font-bold">&lt;</span>
                            @else
                                <a href="{{ $peminjamans->previousPageUrl() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded transition-colors font-bold">&lt;</a>
                            @endif
                            
                            <span class="text-gray-700 font-medium px-3">{{ $peminjamans->currentPage() }}</span>
                            
                            @if ($peminjamans->hasMorePages())
                                <a href="{{ $peminjamans->nextPageUrl() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded transition-colors font-bold">&gt;</a>
                            @else
                                <span class="bg-gray-200 text-gray-400 px-3 py-1 rounded cursor-not-allowed font-bold">&gt;</span>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Setting Modal Popup -->
        <div x-show="settingModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="settingModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="settingModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="settingModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form action="{{ route('peminjaman.settings') }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 border-b pb-3" id="modal-title">
                                Pengaturan Denda
                            </h3>
                            <div class="mt-2 space-y-4">
                                <div>
                                    <label for="toleransi_hari" class="block text-sm font-medium text-gray-700 mb-1">Toleransi (Hari)</label>
                                    <input type="number" name="toleransi_hari" id="toleransi_hari" x-model="settingForm.toleransi_hari" min="0" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                <div>
                                    <label for="denda_per_hari" class="block text-sm font-medium text-gray-700 mb-1">Denda Per Hari (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="denda_per_hari" id="denda_per_hari" x-model="settingForm.denda_per_hari" min="0" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end space-x-3 rounded-b-2xl">
                            <button type="button" @click="settingModalOpen = false" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                Batalkan
                            </button>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm transition-colors">
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
                    <form :action="`/peminjaman/${deleteForm.id}`" method="POST">
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
                                    Yakin ingin menghapus peminjaman ini?
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

        <!-- Return Book Modal Popup -->
        <div x-show="returnModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="returnModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="returnModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="returnModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form :action="`/peminjaman/${returnForm.id}/return`" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-8 pb-6 sm:p-6 sm:pb-6 text-center">
                            <div class="flex justify-center mb-6">
                                <div class="h-24 w-24 rounded-full border-4 border-green-500 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2 mb-2">
                                <h3 class="text-2xl leading-6 font-bold text-gray-900" id="modal-title">
                                    Tandai buku telah dikembalikan?
                                </h3>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-center space-x-4 rounded-b-2xl">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors">
                                Lanjutkan
                            </button>
                            <button type="button" @click="returnModalOpen = false" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
