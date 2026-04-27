@extends('layouts.library')

@section('title', 'Daftar Buku')

@section('content')
    <section class="page-head">
        <div>
            <h1 class="page-title">Daftar Buku</h1>
            <p class="page-subtitle">Kelola data buku perpustakaan mini dari satu halaman.</p>
        </div>

        <a href="{{ route('buku.create') }}" class="btn btn-primary">Tambah Buku</a>
    </section>

    <section class="card">
        @if ($bukus->isEmpty())
            <div class="empty-state">
                <h3>Belum Ada Data Buku</h3>
                <p>Mulai isi koleksi perpustakaan dengan menambahkan buku pertama.</p>
                <a href="{{ route('buku.create') }}" class="btn btn-primary">Isi Data Buku</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bukus as $index => $buku)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->pengarang }}</td>
                                <td>{{ $buku->tahun_terbit }}</td>
                                <td>{{ $buku->stok }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('buku.edit', $buku) }}" class="btn btn-edit">Edit</a>

                                        <form method="POST" action="{{ route('buku.destroy', $buku) }}" class="inline-form" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
