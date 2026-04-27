<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Perpustakaan Mini')</title>

        <link rel="stylesheet" href="{{ asset('css/library.css') }}">
    </head>
    <body>
        <header class="topbar">
            <div class="container topbar-inner">
                <a href="{{ route('buku.index') }}" class="brand">Perpustakaan Mini</a>

                <nav class="menu">
                    <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.index') ? 'is-active' : '' }}">
                        Daftar Buku
                    </a>
                    <a href="{{ route('buku.create') }}" class="{{ request()->routeIs('buku.create') ? 'is-active' : '' }}">
                        Tambah Buku
                    </a>
                    <a href="{{ route('dashboard') }}">Dashboard</a>

                    <form method="POST" action="{{ route('logout') }}" class="inline-form">
                        @csrf
                        <button type="submit" class="link-btn">Logout</button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="container page">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>Periksa input berikut:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
