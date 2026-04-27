<?php
$active = 'beranda';
require_once 'auth_check.php';
require_once __DIR__ . '/../admin/cover_helper.php';

// Greeting berdasarkan waktu
$hour = (int)date('H');
if ($hour >= 5 && $hour < 12) {
    $greeting = 'Selamat Pagi';
} elseif ($hour >= 12 && $hour < 15) {
    $greeting = 'Selamat Siang';
} elseif ($hour >= 15 && $hour < 18) {
    $greeting = 'Selamat Sore';
} else {
    $greeting = 'Selamat Malam';
}

// Get all categories
$kategori_result = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Hitung total denda user
$setting_toleransi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'toleransi_hari'"));
$setting_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'denda_per_hari'"));
$toleransi = (int)($setting_toleransi['nilai'] ?? 0);
$denda_per_hari = (int)($setting_denda['nilai'] ?? 0);

$total_denda = 0;
$pinjaman_aktif = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_user = " . (int)$_SESSION['user_id'] . " AND status_peminjaman = 'dipinjam'");
while ($p = mysqli_fetch_assoc($pinjaman_aktif)) {
    $tgl_kembali = new DateTime($p['tanggal_pengembalian']);
    $hari_ini = new DateTime(date('Y-m-d'));
    $selisih = (int)$hari_ini->diff($tgl_kembali)->format('%r%a');
    $hari_telat = -$selisih - $toleransi;
    if ($hari_telat > 0) {
        $total_denda += $hari_telat * $denda_per_hari;
    }
}
$kategoris = [];
while ($k = mysqli_fetch_assoc($kategori_result)) {
    $kategoris[] = $k;
}

// Filter kategori
$filter_kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

// Get books
if ($filter_kategori > 0) {
    $buku_query = "SELECT b.*, k.nama_kategori FROM buku b JOIN kategori k ON b.id_kategori = k.id_kategori WHERE b.id_kategori = $filter_kategori ORDER BY b.id_buku DESC";
} else {
    $buku_query = "SELECT b.*, k.nama_kategori FROM buku b JOIN kategori k ON b.id_kategori = k.id_kategori ORDER BY b.id_buku DESC";
}
$buku_result = mysqli_query($conn, $buku_query);

// Search
$search = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['q']));
    $buku_query = "SELECT b.*, k.nama_kategori FROM buku b JOIN kategori k ON b.id_kategori = k.id_kategori WHERE (b.judul LIKE '%$search%' OR b.penulis LIKE '%$search%') ";
    if ($filter_kategori > 0) {
        $buku_query .= "AND b.id_kategori = $filter_kategori ";
    }
    $buku_query .= "ORDER BY b.id_buku DESC";
    $buku_result = mysqli_query($conn, $buku_query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan 40</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .main-content { margin-left: 260px; padding: 30px; }
        @media (max-width: 768px) {
            .sidebar-user { width: 100% !important; position: relative !important; min-height: auto !important; }
            .main-content { margin-left: 0; }
        }

        .greeting-card {
            background: linear-gradient(135deg, #020024 0%, #090979 50%, #1a1a6e 100%);
            border-radius: 16px;
            color: #fff;
            padding: 2rem 2.5rem;
        }

        .book-scroll-wrapper {
            position: relative;
        }
        .book-scroll {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 10px 0;
            scrollbar-width: none;
        }
        .book-scroll::-webkit-scrollbar { display: none; }

        .book-card {
            min-width: 180px;
            max-width: 180px;
            flex-shrink: 0;
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

        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: all 0.15s;
        }
        .scroll-btn:hover { background: #f0f0f0; }
        .scroll-btn.left { left: -18px; }
        .scroll-btn.right { right: -18px; }

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

        .search-box {
            max-width: 400px;
        }
        .search-box .form-control {
            border-radius: 20px 0 0 20px;
            border-right: 0;
        }
        .search-box .btn {
            border-radius: 0 20px 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Top bar with search -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="text-muted" style="font-size: 0.85rem;">
                    <i class="bi bi-calendar3 me-1"></i><?= date('l, d F Y') ?>
                </span>
            </div>
            <form method="GET" class="search-box d-flex">
                <?php if ($filter_kategori > 0): ?>
                    <input type="hidden" name="kategori" value="<?= $filter_kategori ?>">
                <?php endif; ?>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari buku..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <!-- Greeting Card -->
        <div class="greeting-card mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1"><?= $greeting ?>, <?= htmlspecialchars($_SESSION['nama']) ?>! 🎉</h3>
                    <p class="mb-0 opacity-75">Selamat datang di portal anggota Perpustakaan 40! >_<</p>
                </div>
                <div class="text-center" style="background: #ffc107; color: #000; border-radius: 14px; padding: 12px 24px; min-width: 140px;">
                    <small class="fw-semibold d-block" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Denda</small>
                    <span class="fw-bold" style="font-size: 1.15rem;">Rp <?= number_format($total_denda, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Buku -->
        <div class="mb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="fw-bold mb-0">Rekomendasi Buku</h5>
                    <small class="text-muted">Temukan inspirasi baca kamu!</small>
                </div>
            </div>

            <!-- Filter kategori -->
            <div class="d-flex gap-2 flex-wrap mb-3">
                <a href="dashboard.php<?= $search ? '?q=' . urlencode($search) : '' ?>" class="filter-btn <?= $filter_kategori == 0 ? 'active-filter' : '' ?>">Semua</a>
                <?php foreach ($kategoris as $kat): ?>
                    <a href="dashboard.php?kategori=<?= $kat['id_kategori'] ?><?= $search ? '&q=' . urlencode($search) : '' ?>" class="filter-btn <?= $filter_kategori == $kat['id_kategori'] ? 'active-filter' : '' ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Book carousel -->
            <div class="book-scroll-wrapper">
                <button class="scroll-btn left" onclick="scrollBooks(-1)"><i class="bi bi-chevron-left"></i></button>
                <div class="book-scroll" id="bookScroll">
                    <?php if (mysqli_num_rows($buku_result) > 0): ?>
                        <?php while ($buku = mysqli_fetch_assoc($buku_result)): ?>
                            <?php
                                $cover = getCover($buku['id_buku']);
                                $star_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM favorit WHERE id_buku = " . (int)$buku['id_buku']))['total'];
                                $user_starred = mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM favorit WHERE id_user = " . (int)$_SESSION['user_id'] . " AND id_buku = " . (int)$buku['id_buku'])) > 0;
                                $is_borrowed = mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM peminjaman WHERE id_user = " . (int)$_SESSION['user_id'] . " AND id_buku = " . (int)$buku['id_buku'] . " AND status_peminjaman = 'dipinjam'")) > 0;
                                // Hitung sisa stok: stok - jumlah peminjaman aktif
                                $active_borrows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = " . (int)$buku['id_buku'] . " AND status_peminjaman = 'dipinjam'"))['total'];
                                $sisa_stok = (int)$buku['stok'] - (int)$active_borrows;
                            ?>
                            <div class="book-card" style="cursor:pointer;"
                                 data-id="<?= $buku['id_buku'] ?>"
                                 data-judul="<?= htmlspecialchars($buku['judul'], ENT_QUOTES) ?>"
                                 data-penulis="<?= htmlspecialchars($buku['penulis'], ENT_QUOTES) ?>"
                                 data-kategori="<?= htmlspecialchars($buku['nama_kategori'], ENT_QUOTES) ?>"
                                 data-deskripsi="<?= htmlspecialchars($buku['deskripsi'], ENT_QUOTES) ?>"
                                 data-cover="<?= $cover ? '../cover/' . $cover : '' ?>"
                                 data-stars="<?= $star_count ?>"
                                 data-starred="<?= $user_starred ? '1' : '0' ?>"
                                 data-borrowed="<?= $is_borrowed ? '1' : '0' ?>"
                                 data-stok="<?= $sisa_stok ?>"
                                 onclick="showBookDetail(this)">
                                <div class="book-cover-wrap">
                                    <?php if ($cover): ?>
                                        <img src="../cover/<?= $cover ?>" alt="<?= htmlspecialchars($buku['judul']) ?>">
                                    <?php else: ?>
                                        <div class="no-cover"><i class="bi bi-book"></i></div>
                                    <?php endif; ?>
                                    <span class="kategori-badge"><?= htmlspecialchars($buku['nama_kategori']) ?></span>
                                </div>
                                <div class="book-info">
                                    <h6 title="<?= htmlspecialchars($buku['judul']) ?>"><?= htmlspecialchars($buku['judul']) ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small><?= htmlspecialchars($buku['penulis']) ?></small>
                                        <span class="d-flex align-items-center gap-1" style="font-size: 0.7rem; color: #6c757d;">
                                            <i class="bi <?= $user_starred ? 'bi-star-fill text-warning' : 'bi-star' ?>"></i><?= $star_count ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-5 w-100">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Belum ada buku<?= $search ? ' untuk pencarian "' . htmlspecialchars($search) . '"' : '' ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="scroll-btn right" onclick="scrollBooks(1)"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Book Detail Modal -->
    <div class="modal fade" id="bookDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index:10;"></button>
                    <div class="d-flex flex-column flex-md-row align-items-center p-4 gap-4">
                        <!-- Cover -->
                        <div id="modalCoverWrap" class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-4 overflow-hidden" style="width: 180px; height: 250px; background: #f0f0f0;">
                            <img id="modalCover" src="" alt="" style="width: 100%; height: 100%; object-fit: contain;">
                            <div id="modalNoCover" class="text-muted" style="font-size: 3rem; display:none;"><i class="bi bi-book"></i></div>
                        </div>
                        <!-- Info -->
                        <div class="flex-grow-1 d-flex flex-column justify-content-center text-center text-md-start">
                            <h4 id="modalJudul" class="fw-bold mb-1"></h4>
                            <p id="modalPenulis" class="text-muted mb-3" style="font-size: 0.95rem;"></p>
                            <div class="mb-3">
                                <span id="modalKategori" class="badge rounded-pill" style="background: rgba(2, 0, 36, 0.8); font-size: 0.75rem; padding: 5px 14px;"></span>
                            </div>
                            <div class="d-flex justify-content-center justify-content-md-start">
                                <button id="modalPinjamBtn" class="btn rounded-pill w-100" style="max-width: 300px;" onclick="handlePinjamBtn()">
                                    <i id="modalPinjamIcon" class="bi me-1"></i><span id="modalPinjamText"></span>
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-3 justify-content-center justify-content-md-start">
                                <button id="modalStarBtn" class="btn btn-sm p-0 border-0" onclick="toggleStar()" style="font-size: 1.3rem; background: none;">
                                    <i id="modalStarIcon" class="bi bi-star"></i>
                                </button>
                                <span id="modalStarCount" class="text-muted" style="font-size: 0.85rem;"></span>
                            </div>
                        </div>
                    </div>
                    <!-- Sinopsis -->
                    <div class="px-4 pb-4 pt-3" style="border-top: 1px solid #eee; margin-top: 0;">
                        <h6 class="fw-bold mb-2">Sinopsis:</h6>
                        <p id="modalDeskripsi" class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.7;"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrow Confirmation Modal -->
    <div class="modal fade" id="borrowConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-book-half text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-center">Pinjam Buku</h5>

                    <!-- Book Info -->
                    <div class="d-flex gap-3 mb-4 p-3 rounded-3" style="background: #f8f9fa;">
                        <div id="borrowModalCoverWrap" class="flex-shrink-0 rounded overflow-hidden" style="width: 60px; height: 85px; background: #e9ecef;">
                            <img id="borrowModalCover" src="" alt="" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <h6 id="borrowModalJudul" class="fw-bold mb-1" style="font-size: 0.95rem;"></h6>
                            <small id="borrowModalPenulis" class="text-muted"></small>
                            <div class="mt-1">
                                <span id="borrowModalKategori" class="badge rounded-pill" style="background: rgba(2, 0, 36, 0.8); font-size: 0.65rem;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Duration Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Durasi Peminjaman</label>
                        <select id="borrowDurasi" class="form-select">
                            <option value="7">7 hari</option>
                            <option value="14">14 hari</option>
                            <option value="21">21 hari</option>
                            <option value="30">30 hari</option>
                        </select>
                        <small class="text-muted">Maksimal peminjaman 30 hari</small>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batalkan</button>
                        <button class="btn btn-primary rounded-pill px-4" onclick="confirmBorrow()" style="background: #020024; border-color: #020024;">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
    <div class="modal fade" id="returnConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-center">Kembalikan Buku?</h5>

                    <!-- Book Info -->
                    <div class="d-flex gap-3 mb-4 p-3 rounded-3" style="background: #f8f9fa;">
                        <div id="returnModalCoverWrap" class="flex-shrink-0 rounded overflow-hidden" style="width: 60px; height: 85px; background: #e9ecef;">
                            <img id="returnModalCover" src="" alt="" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <h6 id="returnModalJudul" class="fw-bold mb-1" style="font-size: 0.95rem;"></h6>
                            <small id="returnModalPenulis" class="text-muted"></small>
                            <div class="mt-1">
                                <span id="returnModalKategori" class="badge rounded-pill" style="background: rgba(2, 0, 36, 0.8); font-size: 0.65rem;"></span>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mb-4 text-center" style="font-size: 0.9rem;">Yakin ingin mengembalikan buku ini?</p>

                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batalkan</button>
                        <button class="btn btn-danger rounded-pill px-4" onclick="confirmReturn()">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function scrollBooks(direction) {
            const container = document.getElementById('bookScroll');
            const scrollAmount = 400;
            container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }

        function showBookDetail(el) {
            const cover = el.dataset.cover;
            const coverImg = document.getElementById('modalCover');
            const noCover = document.getElementById('modalNoCover');
            if (cover) {
                coverImg.src = cover;
                coverImg.style.display = 'block';
                noCover.style.display = 'none';
            } else {
                coverImg.style.display = 'none';
                noCover.style.display = 'block';
            }
            document.getElementById('modalJudul').textContent = el.dataset.judul;
            document.getElementById('modalPenulis').textContent = el.dataset.penulis;
            document.getElementById('modalKategori').textContent = el.dataset.kategori;
            document.getElementById('modalDeskripsi').textContent = el.dataset.deskripsi || 'Tidak ada sinopsis.';

            // Star
            currentBookId = el.dataset.id;
            currentBookEl = el;
            const starred = el.dataset.starred === '1';
            const starCount = parseInt(el.dataset.stars) || 0;
            updateStarUI(starred, starCount);

            // Pinjam/Kembalikan button
            const borrowed = el.dataset.borrowed === '1';
            const stok = parseInt(el.dataset.stok) || 0;
            const pinjamBtn = document.getElementById('modalPinjamBtn');
            const pinjamIcon = document.getElementById('modalPinjamIcon');
            const pinjamText = document.getElementById('modalPinjamText');
            pinjamBtn.disabled = false;
            if (borrowed) {
                pinjamBtn.style.background = '#dc3545';
                pinjamBtn.style.borderColor = '#dc3545';
                pinjamBtn.style.color = '#fff';
                pinjamBtn.className = 'btn rounded-pill w-100';
                pinjamIcon.className = 'bi bi-arrow-return-left me-1';
                pinjamText.textContent = 'Kembalikan Buku';
            } else if (stok <= 0) {
                pinjamBtn.style.background = '#adb5bd';
                pinjamBtn.style.borderColor = '#adb5bd';
                pinjamBtn.style.color = '#fff';
                pinjamBtn.className = 'btn rounded-pill w-100';
                pinjamBtn.disabled = true;
                pinjamIcon.className = 'bi bi-x-circle me-1';
                pinjamText.textContent = 'Stok Habis';
            } else {
                pinjamBtn.style.background = '#020024';
                pinjamBtn.style.borderColor = '#020024';
                pinjamBtn.style.color = '#fff';
                pinjamBtn.className = 'btn rounded-pill w-100';
                pinjamIcon.className = 'bi bi-book me-1';
                pinjamText.textContent = 'Pinjam Buku';
            }

            new bootstrap.Modal(document.getElementById('bookDetailModal')).show();
        }

        let currentBookId = null;
        let currentBookEl = null;

        function updateStarUI(starred, count) {
            const icon = document.getElementById('modalStarIcon');
            icon.className = starred ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted';
            document.getElementById('modalStarCount').textContent = count + ' orang menyukai';
        }

        function toggleStar() {
            if (!currentBookId) return;
            const formData = new FormData();
            formData.append('id_buku', currentBookId);

            fetch('toggle_star.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        updateStarUI(data.starred, data.count);
                        currentBookEl.dataset.starred = data.starred ? '1' : '0';
                        currentBookEl.dataset.stars = data.count;
                    }
                });
        }

        function handlePinjamBtn() {
            if (!currentBookEl) return;

            const borrowed = currentBookEl.dataset.borrowed === '1';
            const stok = parseInt(currentBookEl.dataset.stok) || 0;

            if (borrowed) {
                // Show return confirmation modal
                document.getElementById('returnModalCover').src = currentBookEl.dataset.cover || '';
                document.getElementById('returnModalJudul').textContent = currentBookEl.dataset.judul;
                document.getElementById('returnModalPenulis').textContent = currentBookEl.dataset.penulis;
                document.getElementById('returnModalKategori').textContent = currentBookEl.dataset.kategori;
                bootstrap.Modal.getInstance(document.getElementById('bookDetailModal')).hide();
                new bootstrap.Modal(document.getElementById('returnConfirmModal')).show();
            } else if (stok > 0) {
                // Show borrow confirmation modal
                document.getElementById('borrowModalCover').src = currentBookEl.dataset.cover || '';
                document.getElementById('borrowModalJudul').textContent = currentBookEl.dataset.judul;
                document.getElementById('borrowModalPenulis').textContent = currentBookEl.dataset.penulis;
                document.getElementById('borrowModalKategori').textContent = currentBookEl.dataset.kategori;
                document.getElementById('borrowDurasi').value = '7';
                bootstrap.Modal.getInstance(document.getElementById('bookDetailModal')).hide();
                new bootstrap.Modal(document.getElementById('borrowConfirmModal')).show();
            }
        }

        function confirmBorrow() {
            if (!currentBookId) return;

            const durasi = document.getElementById('borrowDurasi').value;
            const formData = new FormData();
            formData.append('id_buku', currentBookId);
            formData.append('action', 'pinjam');
            formData.append('durasi', durasi);

            fetch('pinjam_buku.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('borrowConfirmModal')).hide();
                    if (data.success) {
                        alert('Buku berhasil dipinjam! Tanggal pengembalian: ' + data.tanggal_kembali);
                        location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan');
                    console.error(err);
                });
        }

        function confirmReturn() {
            if (!currentBookId) return;

            const formData = new FormData();
            formData.append('id_buku', currentBookId);
            formData.append('action', 'kembalikan');

            fetch('pinjam_buku.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('returnConfirmModal')).hide();
                    if (data.success) {
                        let msg = 'Buku berhasil dikembalikan!';
                        if (data.denda > 0) {
                            msg += ' Denda: Rp ' + data.denda.toLocaleString('id-ID');
                        }
                        alert(msg);
                        location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan');
                    console.error(err);
                });
        }
    </script>
</body>
</html>
