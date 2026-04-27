<?php
$active = 'koleksi';
require_once 'auth_check.php';
require_once __DIR__ . '/../admin/cover_helper.php';

$id_user = (int)$_SESSION['user_id'];

// Filter status
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query
$query = "SELECT p.*, b.judul, b.penulis, b.deskripsi, b.id_kategori, k.nama_kategori 
          FROM peminjaman p 
          JOIN buku b ON p.id_buku = b.id_buku 
          JOIN kategori k ON b.id_kategori = k.id_kategori 
          WHERE p.id_user = $id_user";

if ($filter_status === 'dipinjam') {
    $query .= " AND p.status_peminjaman = 'dipinjam'";
} elseif ($filter_status === 'dikembalikan') {
    $query .= " AND p.status_peminjaman = 'dikembalikan'";
}

$query .= " ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksiku - Perpustakaan 40</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .main-content { margin-left: 260px; padding: 30px; }
        @media (max-width: 768px) {
            .sidebar-user { width: 100% !important; position: relative !important; min-height: auto !important; }
            .main-content { margin-left: 0; }
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

        .book-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }
        .book-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
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
        .status-badge {
            position: absolute;
            bottom: 8px;
            left: 8px;
            font-size: 0.65rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .book-info {
            padding: 12px;
        }
        .book-info h6 {
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .book-info small {
            color: #6c757d;
            font-size: 0.75rem;
        }

        .filter-dropdown {
            position: relative;
            display: inline-block;
        }
        .filter-popup {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 6px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            min-width: 180px;
            z-index: 100;
            padding: 8px 0;
        }
        .filter-popup.show { display: block; }
        .filter-popup-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 16px;
            font-size: 0.85rem;
            color: #333;
            cursor: pointer;
            position: relative;
        }
        .filter-popup-item:hover { background: #f5f5f5; }

        .filter-sub-popup {
            display: none;
            position: absolute;
            left: -185px;
            top: -8px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            min-width: 170px;
            z-index: 101;
            padding: 8px 0;
        }
        .filter-popup-item:hover .filter-sub-popup { display: block; }
        .filter-sub-item {
            display: block;
            padding: 8px 16px;
            font-size: 0.85rem;
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }
        .filter-sub-item:hover { background: #f5f5f5; color: #333; }
        .filter-sub-item.active-sub { color: #020024; font-weight: 600; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="fw-bold mb-0">Koleksiku</h3>
        </div>

        <!-- Tabs + Filter -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <a href="koleksi.php<?= $filter_status ? '?status=' . urlencode($filter_status) : '' ?>" class="filter-btn active-filter">Semua</a>
                <span class="filter-btn" style="opacity: 0.5; cursor: default;">E-book</span>
            </div>

            <div class="filter-dropdown">
                <button class="btn btn-sm btn-dark rounded-pill px-3" onclick="toggleFilterPopup(event)" style="font-size: 0.8rem;">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <div class="filter-popup" id="filterPopup">
                    <div class="filter-popup-item">
                        <span>Status:</span>
                        <i class="bi bi-chevron-left" style="font-size: 0.7rem;"></i>
                        <div class="filter-sub-popup">
                            <a href="koleksi.php" class="filter-sub-item <?= $filter_status === '' ? 'active-sub' : '' ?>">Semua</a>
                            <a href="koleksi.php?status=dipinjam" class="filter-sub-item <?= $filter_status === 'dipinjam' ? 'active-sub' : '' ?>">Dipinjam</a>
                            <a href="koleksi.php?status=dikembalikan" class="filter-sub-item <?= $filter_status === 'dikembalikan' ? 'active-sub' : '' ?>">Dikembalikan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($filter_status): ?>
            <div class="mb-3">
                <span class="badge rounded-pill bg-light text-dark border" style="font-size: 0.8rem;">
                    Status: <?= $filter_status === 'dipinjam' ? 'Dipinjam' : 'Dikembalikan' ?>
                    <a href="koleksi.php" class="text-dark ms-1" style="text-decoration: none;">&times;</a>
                </span>
            </div>
        <?php endif; ?>

        <!-- Book grid -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="book-card-grid">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php $cover = getCover($row['id_buku']); ?>
                    <div class="book-card">
                        <div class="book-cover-wrap">
                            <?php if ($cover): ?>
                                <img src="../cover/<?= $cover ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                            <?php else: ?>
                                <div class="no-cover"><i class="bi bi-book"></i></div>
                            <?php endif; ?>
                            <span class="kategori-badge"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                            <?php if ($row['status_peminjaman'] === 'dipinjam'): ?>
                                <span class="status-badge bg-warning text-dark">Dipinjam</span>
                            <?php else: ?>
                                <span class="status-badge bg-success text-white">Dikembalikan</span>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h6 title="<?= htmlspecialchars($row['judul']) ?>"><?= htmlspecialchars($row['judul']) ?></h6>
                            <small><?= htmlspecialchars($row['penulis']) ?></small>
                            <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                <?= date('d/m/Y', strtotime($row['tanggal_peminjaman'])) ?> — <?= date('d/m/Y', strtotime($row['tanggal_pengembalian'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">Belum ada buku di koleksimu<?= $filter_status ? ' dengan status "' . htmlspecialchars($filter_status) . '"' : '' ?></p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFilterPopup(e) {
            e.stopPropagation();
            document.getElementById('filterPopup').classList.toggle('show');
        }
        document.addEventListener('click', function() {
            document.getElementById('filterPopup').classList.remove('show');
        });
    </script>
</body>
</html>
