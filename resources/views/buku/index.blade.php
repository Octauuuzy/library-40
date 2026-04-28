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

                    <div class="mb-4 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">Daftar Buku</h2>
                        <a href="{{ route('buku.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors shadow-sm">
                            + Tambah Buku
                        </a>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-sm leading-normal">
                                    <th class="py-3 px-6 text-left border-b font-semibold">No</th>
                                    <th class="py-3 px-6 text-left border-b font-semibold">Judul</th>
                                    <th class="py-3 px-6 text-left border-b font-semibold">Pengarang</th>
                                    <th class="py-3 px-6 text-center border-b font-semibold">Tahun Terbit</th>
                                    <th class="py-3 px-6 text-center border-b font-semibold">Stok</th>
                                    <th class="py-3 px-6 text-center border-b font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($bukus as $key => $buku)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ $key + 1 }}</td>
                                        <td class="py-3 px-6 text-left"><span class="font-medium text-gray-800">{{ $buku->judul }}</span></td>
                                        <td class="py-3 px-6 text-left">{{ $buku->pengarang }}</td>
                                        <td class="py-3 px-6 text-center">{{ $buku->tahun_terbit }}</td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">{{ $buku->stok }}</span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('buku.edit', $buku->id) }}" class="text-amber-500 hover:text-amber-700 font-medium">
                                                    Edit
                                                </a>
                                                <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($bukus->isEmpty())
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data buku.</td>
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