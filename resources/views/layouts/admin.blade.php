<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-[#1e293b] text-white flex-shrink-0 fixed h-full z-20">
            <div class="flex items-center px-6 py-3 border-b border-gray-700 h-16">
                <div class="flex items-center space-x-3">
                    <!-- Logo -->
                    <div class="w-[42px] h-[42px] flex items-center justify-center">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <span class="text-lg font-bold tracking-wider">Perpus Digital</span>
                </div>
            </div>
            
            <div class="py-4">
                <nav class="space-y-1 px-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('anggota.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('anggota.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Anggota
                    </a>
                    <a href="{{ route('kategori.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('kategori.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Kategori
                    </a>
                    <a href="{{ route('buku.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('buku.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Buku
                    </a>
                    <a href="{{ route('peminjaman.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('peminjaman.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Peminjaman
                    </a>
                </nav>
            </div>
            
            <!-- User Menu Bottom -->
            <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-red-900/20 hover:text-red-300 transition-colors">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 flex flex-col min-h-screen bg-gray-50">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 z-10">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-md border border-gray-200 flex items-center font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </span>
                    <div class="text-sm font-medium text-gray-700">
                        Halo, {{ Auth::check() ? Auth::user()->name : 'Tamu (Tanpa Akun)' }}
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-8 flex-1 overflow-y-auto">
                @if(isset($header))
                    <div class="mb-6 text-gray-600">
                        {{ $header }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>