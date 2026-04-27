<?php
$active = 'anggota';
require_once 'auth_check.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Tidak boleh hapus akun sendiri
    if ($id == $_SESSION['user_id']) {
        $error = "Tidak dapat menghapus akun sendiri!";
    } else {
        // Cek apakah anggota punya peminjaman aktif
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_user = $id AND status_peminjaman = 'dipinjam'"));
        if ($cek['total'] > 0) {
            $error = "Tidak dapat menghapus, user masih memiliki peminjaman aktif!";
        } else {
            // Hapus peminjaman terkait dulu
            mysqli_query($conn, "DELETE FROM peminjaman WHERE id_user = $id");
            mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");
            header("Location: anggota.php?success=delete");
            exit();
        }
    }
}

// Get all user
$query = "SELECT * FROM user ORDER BY id_user DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .main-content { margin-left: 260px; padding: 20px; }
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
            <h2 class="fw-bold mb-0">Data Anggota</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="window.print()" title="Print">
                    <i class="bi bi-printer"></i>
                </button>
                <a href="anggota_tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Anggota
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                    if ($_GET['success'] == 'add') echo "Anggota berhasil ditambahkan!";
                    elseif ($_GET['success'] == 'edit') echo "Anggota berhasil diperbarui!";
                    elseif ($_GET['success'] == 'delete') echo "Anggota berhasil dihapus!";
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
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID User</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= str_pad($row['id_user'], 7, '0', STR_PAD_LEFT) ?></code></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td><?= htmlspecialchars($row['no_handphone']) ?></td>
                                    <td>
                                        <?php if ($row['level'] == 'admin'): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Anggota</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="anggota_edit.php?id=<?= $row['id_user'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($row['id_user'] != $_SESSION['user_id']): ?>
                                        <a href="anggota.php?delete=<?= $row['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada data anggota</td>
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
