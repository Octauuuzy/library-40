@extends('layouts.library')

@section('title', 'Edit Buku')

@section('content')
    <section class="page-head">
        <div>
            <h1 class="page-title">Edit Buku</h1>
            <p class="page-subtitle">Perbarui data buku yang sudah ada.</p>
        </div>
    </section>

    <section class="card">
        <div class="card-content">
            @include('buku._form', ['buku' => $buku])
        </div>
    </section>
@endsection
