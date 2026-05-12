<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Portal Anggota</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between h-full flex-shrink-0 z-20 shadow-sm">
        <div>
            <!-- Header Logo -->
            <div class="h-20 flex items-center px-6 border-b border-gray-100">
                <div class="w-10 h-10 flex items-center justify-center mr-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-extrabold text-gray-800 text-[15px] leading-tight tracking-tight">Perpus Digital</h1>
                    <p class="text-[11px] text-gray-500 font-medium tracking-wide">Portal Anggota</p>
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="px-4 py-6 space-y-2">
                <p class="text-[10px] font-bold text-gray-400 tracking-wider mb-4 px-2 uppercase">Utama</p>
                <a href="{{ route('katalog') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium bg-[#233757] text-white shadow-sm transition-all">
                    <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-[#233757] transition-all">
                    <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Koleksi
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-[#233757] transition-all">
                    <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Favorit
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-[#233757] transition-all">
                    <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    E-book
                </a>
            </div>
        </div>

        <!-- User Profile Bottom -->
        <div>
            <div class="p-4 border-t border-gray-100 flex items-center">
                <!-- Avatar -->
                <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="ml-3 overflow-hidden">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="px-4 pb-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main View Area -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-[#f4f7f6]">
        
        <!-- Header Topbar -->
        <header class="h-20 flex items-center justify-between px-10 border-b border-gray-200 bg-white/50 backdrop-blur-sm z-10 sticky top-0">
            <div class="flex items-center text-gray-500 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
            
            <div class="relative w-80">
                <input type="text" placeholder="Cari buku..." class="w-full pl-5 pr-12 py-2.5 border border-gray-300 rounded-full text-sm focus:ring-[#233757] focus:border-[#233757] shadow-sm transition-shadow">
                <button class="absolute right-0 top-0 h-full w-12 bg-blue-600 text-white rounded-r-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Scrollable Body Content -->
        <div class="flex-1 overflow-y-auto px-10 pb-12 pt-8">
            @yield('content')
        </div>
        
    </main>

</body>
</html>