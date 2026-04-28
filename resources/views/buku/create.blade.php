@extends('layouts.admin')

@section('content')
    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Tambah Buku Baru</h2>
                    
                    <form action="{{ route('buku.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="judul" class="block text-gray-700 text-sm font-medium mb-2">Judul Buku</label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul') }}" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('judul') border-red-500 @enderror" required>
                            @error('judul')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="pengarang" class="block text-gray-700 text-sm font-medium mb-2">Pengarang</label>
                            <input type="text" name="pengarang" id="pengarang" value="{{ old('pengarang') }}" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('pengarang') border-red-500 @enderror" required>
                            @error('pengarang')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="tahun_terbit" class="block text-gray-700 text-sm font-medium mb-2">Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit') }}" placeholder="2024" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tahun_terbit') border-red-500 @enderror" required>
                                @error('tahun_terbit')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="stok" class="block text-gray-700 text-sm font-medium mb-2">Stok</label>
                                <input type="number" name="stok" id="stok" value="{{ old('stok') }}" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stok') border-red-500 @enderror" required>
                                @error('stok')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 border-t pt-4">
                            <a href="{{ route('buku.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                                Simpan Buku
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection