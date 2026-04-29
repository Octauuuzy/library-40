@extends('layouts.admin')

@section('content')
    <!-- Tampilan Dashboard Kosong tanpa data apapun ntah DB atau file lokal -->
    <div class="flex flex-col items-center justify-center h-full min-h-[500px]">
        <div class="text-center text-gray-400 bg-white p-12 rounded-xl border border-gray-200 shadow-sm w-full max-w-3xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-500 mb-2">Tidak Ada Data</h2>
        </div>
    </div>
    
    <!-- Jika dia punya akun dan login tapi BUKAN admin (role: anggota), maka muncul peringatan -->
    @if(Auth::check() && Auth::user()->role !== 'admin')
    <div x-data="{ violationModalOpen: true }" x-show="violationModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-red-100">
                <div class="bg-white px-4 pt-10 pb-6 sm:p-8 sm:pb-6 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="h-24 w-24 rounded-full border-4 border-red-500 flex items-center justify-center bg-red-50">
                            <span class="text-red-600 text-6xl font-bold">!</span>
                        </div>
                    </div>
                    <div class="mt-2 mb-2">
                        <h3 class="text-3xl leading-6 font-extrabold text-gray-900" id="modal-title">
                            Pelanggaran
                        </h3>
                        <p class="text-base text-gray-600 mt-4">
                            Hanya Administrator yang berhak memiliki akses halaman/aksi ini
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:px-6 flex justify-center rounded-b-3xl">
                    <button type="button" @click="violationModalOpen = false; window.location.href='/katalog'" class="inline-flex justify-center rounded-xl border border-transparent shadow-md px-10 py-2.5 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection