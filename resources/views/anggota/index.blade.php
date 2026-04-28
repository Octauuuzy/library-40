@extends('layouts.admin')

@section('content')
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
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
                            <a href="{{ route('anggota.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors shadow-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Anggota
                            </a>
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
                                    <th class="py-3 px-4 text-left border-b">Alamat</th>
                                    <th class="py-3 px-4 text-left border-b">No. HP</th>
                                    <th class="py-3 px-4 text-center border-b">Role</th>
                                    <th class="py-3 px-4 text-center border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($anggotas as $key => $anggota)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-left whitespace-nowrap">{{ $key + 1 }}</td>
                                        <td class="py-3 px-4 text-left"><span class="font-medium text-pink-500">{{ str_pad($anggota->id, 7, '0', STR_PAD_LEFT) }}</span></td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->name }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->username }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->email }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->alamat ?? '-' }}</td>
                                        <td class="py-3 px-4 text-left text-gray-800">{{ $anggota->no_hp ?? '0' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($anggota->role == 'admin')
                                                <span class="bg-red-500 text-white py-1 px-3 rounded-md text-xs font-semibold">Admin</span>
                                            @else
                                                <span class="bg-cyan-400 text-white py-1 px-3 rounded-md text-xs font-semibold">Anggota</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('anggota.edit', $anggota->id) }}" class="bg-amber-400 hover:bg-amber-500 text-white p-1.5 rounded-md transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('anggota.destroy', $anggota->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus anggota ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-md transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($anggotas->isEmpty())
                                    <tr>
                                        <td colspan="9" class="py-8 text-center text-gray-500">Belum ada data anggota.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection