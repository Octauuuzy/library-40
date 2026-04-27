<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .sidebar-user {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid #e9ecef;
        }
        .main-content { margin-left: 260px; padding: 30px; }
        @media (max-width: 768px) {
            .sidebar-user { width: 100%; position: relative; min-height: auto; }
            .main-content { margin-left: 0; }
        }
        .sidebar-user .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.15s;
        }
        .sidebar-user .nav-link:hover { background: #f0f0f0; }
        .active-sidebar {
            background: rgba(2, 0, 36, 0.85) !important;
            color: #fff !important;
        }
        .greeting-card {
            background: linear-gradient(135deg, #020024 0%, #090979 50%, #1a1a6e 100%);
            border-radius: 16px;
            color: #fff;
            padding: 2rem 2.5rem;
        }
        .book-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
            height: 100%;
        }
        .book-card:hover { transform: translateY(-4px); }
        .book-cover-wrap {
            position: relative;
            width: 100%;
            height: 240px;
            overflow: hidden;
            background: #e9ecef;
        }
        .book-cover-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .book-cover-wrap .no-cover {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #adb5bd;
            font-size: 3rem;
        }
        .kategori-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(2, 0, 36, 0.8);
            color: #fff;
            font-size: 0.65rem;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
        }
        .filter-btn {
            border-radius: 20px;
            padding: 0.3rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #dee2e6;
            background: #fff;
            color: #333;
            text-decoration: none;
            transition: all 0.15s;
        }
        .filter-btn:hover { background: #f0f0f0; color: #333; }
        .filter-btn.active-filter {
            background: rgba(2, 0, 36, 0.85);
            color: #fff;
            border-color: #020024;
        }
        .search-box { max-width: 400px; }
        .search-box .form-control {
            border-radius: 20px 0 0 20px;
            border-right: 0;
        }
        .search-box .btn { border-radius: 0 20px 20px 0; }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-shrink-0 sidebar-user">
        <div class="p-3">
            <a href="{{ route('user.dashboard') }}" class="d-flex align-items-center text-decoration-none mb-2">
                <i class="bi bi-book-half me-2 fs-4 text-dark"></i>
                <div>
                    <span class="fw-bold text-dark" style="font-size: 1rem;">Perpustakaan 40</span><br>
                    <small class="text-muted" style="font-size: 0.75rem;">Portal Anggota</small>
                </div>
            </a>
        </div>

        <div class="px-3">
            <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Utama</small>
        </div>
        <ul class="nav flex-column px-2 mt-1">
            <li class="nav-item">
                <a href="{{ route('user.dashboard') }}" class="nav-link rounded-3 active-sidebar">
                    <i class="bi bi-house-door me-2"></i>Beranda
                </a>
            </li>
        </ul>

        <div class="mt-auto p-3 border-top">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 600;">
                    {{ strtoupper(substr($user->nama, 0, 2)) }}
                </div>
                <div style="line-height: 1.2;">
                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $user->nama }}</span><br>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-dark btn-sm w-100 mt-1">
                    <i class="bi bi-box-arrow-left me-1"></i>Keluar
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="text-muted" style="font-size: 0.85rem;">
                    <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <form method="GET" class="search-box d-flex">
                @if ($filterKategori > 0)
                    <input type="hidden" name="kategori" value="{{ $filterKategori }}">
                @endif
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari buku..." value="{{ $search }}">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="greeting-card mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1">{{ $greeting }}, {{ $user->nama }}!</h3>
                    <p class="mb-0 opacity-75">Selamat datang di portal anggota Perpustakaan 40!</p>
                </div>
                <div class="text-center" style="background: #ffc107; color: #000; border-radius: 14px; padding: 12px 24px; min-width: 140px;">
                    <small class="fw-semibold d-block" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Denda</small>
                    <span class="fw-bold" style="font-size: 1.15rem;">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="mb-2">
            <div class="mb-2">
                <h5 class="fw-bold mb-0">Rekomendasi Buku</h5>
                <small class="text-muted">Temukan inspirasi baca kamu!</small>
            </div>

            <div class="d-flex gap-2 flex-wrap mb-3">
                <a href="{{ route('user.dashboard', $search !== '' ? ['q' => $search] : []) }}" class="filter-btn {{ $filterKategori === 0 ? 'active-filter' : '' }}">Semua</a>
                @foreach ($kategoris as $kategori)
                    <a href="{{ route('user.dashboard', array_filter(['kategori' => $kategori->id_kategori, 'q' => $search])) }}" class="filter-btn {{ $filterKategori === (int) $kategori->id_kategori ? 'active-filter' : '' }}">
                        {{ $kategori->nama_kategori }}
                    </a>
                @endforeach
            </div>

            <div class="row g-4">
                @forelse ($books as $book)
                    <div class="col-md-4 col-xl-3">
                        <div class="book-card">
                            <div class="book-cover-wrap">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->judul }}">
                                @else
                                    <div class="no-cover"><i class="bi bi-book"></i></div>
                                @endif
                                <span class="kategori-badge">{{ $book->nama_kategori }}</span>
                            </div>
                            <div class="p-3">
                                <h6 class="fw-bold mb-1">{{ $book->judul }}</h6>
                                <small class="text-muted d-block mb-2">{{ $book->penulis }}</small>
                                <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($book->deskripsi ?? 'Tidak ada sinopsis.', 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge text-bg-light">Stok: {{ $book->sisa_stok }}</span>
                                    <span class="small text-muted">
                                        <i class="bi {{ $book->user_starred ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                        {{ $book->star_count }}
                                    </span>
                                </div>
                                @if ($book->is_borrowed)
                                    <div class="mt-2 small text-success fw-semibold">Sedang dipinjam</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5 bg-white rounded-4 shadow-sm">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Belum ada buku{{ $search !== '' ? ' untuk pencarian "' . $search . '"' : '' }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
