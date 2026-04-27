<?php
$active = 'buku';
require_once 'auth_check.php';
require_once 'cover_helper.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = $id AND status_peminjaman = 'dipinjam'"));
    if ($cek['total'] > 0) {
        $error = "Tidak bisa menghapus buku yang sedang dipinjam!";
    } else {
        deleteCover($id);
        mysqli_query($conn, "DELETE FROM peminjaman WHERE id_buku = $id");
        mysqli_query($conn, "DELETE FROM buku WHERE id_buku = $id");
        header("Location: buku.php?success=delete");
        exit();
    }
}

$query = "SELECT b.*, k.nama_kategori FROM buku b 
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
          ORDER BY b.id_buku DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .main-content { margin-left: 260px; padding: 20px; }
        .cover-thumb { width: 50px; height: 70px; object-fit: cover; border-radius: 4px; }
        table code { color: #000; }
        @media (max-width: 768px) {
            .sidebar { width: 100% !important; position: relative !important; min-height: auto !important; }
            .main-content { margin-left: 0; }
        }
        @media print {
            body { background: white; }
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar { display: none; }
            .d-flex { display: none !important; }
            .btn { display: none !important; }
            .mb-4 { margin-bottom: 1rem !important; }
            .card { box-shadow: none; border: none; }
            .card-body { padding: 0 !important; }
            .alert { display: none !important; }
            .cover-thumb { display: none; }
            .print-header { display: block !important; }
            table { width: 100%; font-size: 12px; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
            .print-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            .print-header h1 { margin: 0; font-size: 20px; }
            .print-header p { margin: 5px 0 0 0; font-size: 12px; }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="print-header" style="display: none;">
            <img src="../assets/logo.png" alt="Logo" style="width: 60px; height: 60px; margin-bottom: 10px;">
            <h1>PERPUSTAKAAN 40</h1>
            <p><?= date('d F Y') ?></p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Data Buku</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="window.print()" title="Print">
                    <i class="bi bi-printer"></i>
                </button>
                <a href="buku_tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Buku
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                    if ($_GET['success'] == 'add') echo "Buku berhasil ditambahkan!";
                    elseif ($_GET['success'] == 'edit') echo "Buku berhasil diperbarui!";
                    elseif ($_GET['success'] == 'delete') echo "Buku berhasil dihapus!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID Buku</th>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Sisa Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)):
                                    $cover = getCover($row['id_buku']);
                                    $active_borrows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = " . (int)$row['id_buku'] . " AND status_peminjaman = 'dipinjam'"))['total'];
                                    $sisa = (int)$row['stok'] - (int)$active_borrows;
                                ?>
                                <tr>
                                    <td><code><?= str_pad($row['id_buku'], 7, '0', STR_PAD_LEFT) ?></code></td>
                                    <td>
                                        <?php if ($cover): ?>
                                            <img src="../cover/<?= htmlspecialchars($cover) ?>" alt="Cover" class="cover-thumb">
                                        <?php else: ?>
                                            <div class="cover-thumb bg-secondary d-flex align-items-center justify-content-center text-white">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['judul']) ?></strong>
                                        <br><small class="text-muted"><?= mb_strimwidth(htmlspecialchars($row['deskripsi']), 0, 50, '...') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></span></td>
                                    <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
                                    <td>
                                        <?php if ($row['stok'] <= 0): ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= (int)$row['stok'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($sisa <= 0): ?>
                                            <span class="badge bg-danger">0</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><?= $sisa ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="buku_edit.php?id=<?= $row['id_buku'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="buku.php?delete=<?= $row['id_buku'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada data buku</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
