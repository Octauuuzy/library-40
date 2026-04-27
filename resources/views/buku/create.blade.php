@extends('layouts.library')

@section('title', 'Tambah Buku')

@section('content')
    <section class="page-head">
        <div>
            <h1 class="page-title">Tambah Buku</h1>
            <p class="page-subtitle">Isi form berikut untuk menambahkan buku baru.</p>
        </div>
    </section>

    <section class="card">
        <div class="card-content">
            @include('buku._form')
        </div>
    </section>
@endsection
