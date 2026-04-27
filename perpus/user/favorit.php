<?php
$active = 'favorit';
require_once 'auth_check.php';
require_once __DIR__ . '/../admin/cover_helper.php';

$id_user = (int)$_SESSION['user_id'];

// Get favorited books
$query = "SELECT b.*, k.nama_kategori, 
          (SELECT COUNT(*) FROM favorit WHERE id_buku = b.id_buku) as star_count
          FROM favorit f 
          JOIN buku b ON f.id_buku = b.id_buku 
          JOIN kategori k ON b.id_kategori = k.id_kategori 
          WHERE f.id_user = $id_user 
          ORDER BY f.id_favorit DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoritku - Perpustakaan 40</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .main-content { margin-left: 260px; padding: 30px; }
        @media (max-width: 768px) {
            .sidebar-user { width: 100% !important; position: relative !important; min-height: auto !important; }
            .main-content { margin-left: 0; }
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
            cursor: pointer;
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
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h3 class="fw-bold mb-4">Favoritku</h3>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="book-card-grid">
                <?php while ($buku = mysqli_fetch_assoc($result)): ?>
                    <?php $cover = getCover($buku['id_buku']); ?>
                    <div class="book-card" onclick="removeStar(this, <?= $buku['id_buku'] ?>)">
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
                                    <i class="bi bi-star-fill text-warning"></i><?= $buku['star_count'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-heart" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">Belum ada buku favorit</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeStar(el, idBuku) {
            if (!confirm('Hapus dari favorit?')) return;
            const formData = new FormData();
            formData.append('id_buku', idBuku);
            fetch('toggle_star.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success && !data.starred) {
                        el.remove();
                        if (!document.querySelector('.book-card')) {
                            document.querySelector('.book-card-grid').outerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-heart" style="font-size: 3rem;"></i><p class="mt-2 mb-0">Belum ada buku favorit</p></div>';
                        }
                    }
                });
        }
    </script>
</body>
</html>
