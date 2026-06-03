@extends('layouts.admin')

@section('content')
    <div class="py-4" x-data="{ 
        clearModalOpen: false
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-3xl font-bold text-[#1e293b]">Aktivitas Log</h2>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <div class="flex space-x-3">
                            <button type="button" @click="clearModalOpen = true" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md transition-colors shadow-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-800 text-sm leading-normal font-bold">
                                    <th class="py-3 px-4 text-left border-b w-32">Log ID</th>
                                    <th class="py-3 px-4 text-left border-b">Username</th>
                                    <th class="py-3 px-4 text-left border-b">ID User</th>
                                    <th class="py-3 px-4 text-left border-b">Deskripsi</th>
                                    <th class="py-3 px-4 text-left border-b">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($logs as $log)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-left whitespace-nowrap"><span class="font-medium text-gray-900">{{ $log->log_id }}</span></td>
                                        <td class="py-3 px-4 text-left text-gray-800 font-medium">{{ $log->username ?? 'Sistem / Guest' }}</td>
                                        <td class="py-3 px-4 text-left"><span class="font-medium text-blue-500">{{ $log->user_id ? str_pad($log->user_id, 7, '0', STR_PAD_LEFT) : '-' }}</span></td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $log->deskripsi }}</td>
                                        <td class="py-3 px-4 text-left text-gray-500">{{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-500">Belum ada data log aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination UI -->
                    <div class="mt-6 flex flex-col items-center justify-center space-y-2">
                        <div class="flex items-center space-x-2">
                            @if ($logs->onFirstPage())
                                <button disabled class="bg-gray-100 text-gray-400 px-3 py-1.5 rounded cursor-not-allowed font-bold border border-gray-200">&lt;</button>
                            @else
                                <a href="{{ $logs->previousPageUrl() }}" class="bg-white hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded transition-colors font-bold border border-gray-300 shadow-sm">&lt;</a>
                            @endif
                            
                            <form action="{{ url()->current() }}" method="GET" class="inline-flex m-0">
                                <input type="number" name="page" value="{{ $logs->currentPage() }}" min="1" max="{{ max(1, $logs->lastPage()) }}" class="w-16 text-center border border-gray-300 rounded shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-1.5 font-medium text-gray-700 m-0 p-0" onchange="this.form.submit()">
                            </form>
                            
                            @if ($logs->hasMorePages())
                                <a href="{{ $logs->nextPageUrl() }}" class="bg-white hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded transition-colors font-bold border border-gray-300 shadow-sm">&gt;</a>
                            @else
                                <button disabled class="bg-gray-100 text-gray-400 px-3 py-1.5 rounded cursor-not-allowed font-bold border border-gray-200">&gt;</button>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-500 font-medium">
                            {{ max(1, $logs->lastPage()) }} Page ditemukan
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @if(Auth::check() && Auth::user()->role === 'admin')
        <!-- Clear Logs Modal Popup -->
        <div x-show="clearModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="clearModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="clearModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="clearModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <form action="{{ route('logs.clear') }}" method="POST">
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
                                    Yakin ingin menghapus semua log?
                                </h3>
                                <p class="text-sm text-gray-500 mt-2">
                                    Tindakan ini akan mengosongkan seluruh riwayat aktivitas.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-center space-x-4 rounded-b-2xl">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors">
                                Lanjutkan
                            </button>
                            <button type="button" @click="clearModalOpen = false" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm transition-colors">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
@endsection