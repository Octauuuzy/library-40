@extends('layouts.admin')

@section('content')
    <div class="py-4" x-data="{ 
        addModalOpen: false,
        editModalOpen: false, 
        deleteModalOpen: false,
        deleteModalOpen: false,
        editForm: {
            id: '',
            name: '',
            username: '',
            email: '',
            no_hp: '',
            role: ''
        },
        deleteForm: {
            id: ''
        },
        openEditModal(anggota) {
            this.editForm.id = anggota.id;
            this.editForm.name = anggota.name;
            this.editForm.username = anggota.username;
            this.editForm.email = anggota.email;
            this.editForm.no_hp = anggota.no_hp || '';
            this.editForm.role = anggota.role;
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
                        <h2 class="text-3xl font-bold text-[#1e293b]">Data Anggota</h2>
                        <div class="flex space-x-3">
                            <button type="button" class="bg-white hover:bg-gray-50 text-gray-600 border border-gray-300 font-medium py-2 px-3 rounded-md transition-colors shadow-sm flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </button>
                            <button type="button" @click="addModalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors shadow-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Anggota
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-800 text-sm leading-normal font-bold">
                                    <th class="py-3 px-4 text-left border-b">No</th>
                                    <th class="py-3 px-4 text-left border-b">ID User</th>
                                    <th class="py-3 px-4 text-left border-b">Nama</th>
                                    <th class="py-3 px-4 text-left border-b">Username</th>
                                    <th class="py-3 px-4 text-left border-b">Email</th>
                                    <th class="py-3 px-4 text-left border-b">No. HP</th>
                                    <th class="py-3 px-4 text-center border-b">Role</th>
                                    <th class="py-3 px-4 text-center border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($anggotas as $key => $anggota)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-left whitespace-nowrap">{{ $key + 1 }}</td>
                                        <td class="py-3 px-4 text-left"><span class="font-medium text-gray-900">{{ str_pad($anggota->id, 7, '0', STR_PAD_LEFT) }}</span></td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->name }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->username }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->email }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->no_hp ?? '0' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($anggota->role == 'admin')
                                                <span class="bg-red-500 text-white py-1 px-3 rounded-full text-xs font-semibold">Admin</span>
                                            @else
                                                <span class="bg-cyan-400 text-white py-1 px-3 rounded-full text-xs font-semibold">Anggota</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <button type="button" @click="openEditModal({{ $anggota }})" class="bg-amber-400 hover:bg-amber-500 text-white p-1.5 rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button type="button" @click="openDeleteModal({{ $anggota->id }})" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($anggotas->isEmpty())
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-gray-500">Belum ada data anggota.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if ($anggotas->hasPages())
                        <div class="mt-4 flex justify-center items-center space-x-2">
                            @if ($anggotas->onFirstPage())
                                <span class="bg-gray-200 text-gray-400 px-3 py-1 rounded cursor-not-allowed font-bold">&lt;</span>
                            @else
                                <a href="{{ $anggotas->previousPageUrl() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded transition-colors font-bold">&lt;</a>
                            @endif
                            
                            <span class="text-gray-700 font-medium px-3">{{ $anggotas->currentPage() }}</span>
                            
                            @if ($anggotas->hasMorePages())
                                <a href="{{ $anggotas->nextPageUrl() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded transition-colors font-bold">&gt;</a>
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
                    
                    <form action="{{ route('anggota.store') }}" method="POST">
                        @csrf
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 border-b pb-3" id="modal-title">
                                Tambah Data Anggota
                            </h3>
                            
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="add_name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                    <input type="text" name="name" id="add_name" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                
                                <div>
                                    <label for="add_username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <input type="text" name="username" id="add_username" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div>
                                    <label for="add_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="add_email" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div>
                                    <label for="add_no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                    <input type="number" name="no_hp" id="add_no_hp" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="add_role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select name="role" id="add_role" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                        <option value="anggota">Anggota</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="add_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" id="add_password" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required minlength="8">
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
                
                <!-- Background overlay -->
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="editModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    
                    <form :action="`/anggota/${editForm.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4 border-b pb-3" id="modal-title">
                                Edit Data Anggota
                            </h3>
                            
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                    <input type="text" name="name" id="name" x-model="editForm.name" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>
                                
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <input type="text" name="username" id="username" x-model="editForm.username" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="email" x-model="editForm.email" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                </div>

                                <div>
                                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                    <input type="number" name="no_hp" id="no_hp" x-model="editForm.no_hp" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <select name="role" id="role" x-model="editForm.role" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                        <option value="admin">Admin</option>
                                        <option value="anggota">Anggota</option>
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
                                    <input type="password" name="password" id="edit_password" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" minlength="8">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
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
                
                <!-- Background overlay -->
                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="deleteModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    
                    <form :action="`/anggota/${deleteForm.id}`" method="POST">
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
                                    Yakin ingin menghapus user ini?
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
@endsection