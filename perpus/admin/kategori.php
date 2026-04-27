<?php
$active = 'kategori';
require_once 'auth_check.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku WHERE id_kategori = $id"));
    if ($cek['total'] > 0) {
        $error = "Tidak bisa menghapus kategori yang masih digunakan oleh buku!";
    } else {
        mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = $id");
        header("Location: kategori.php?success=delete");
        exit();
    }
}

// Handle tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    if (empty($nama)) {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
        header("Location: kategori.php?success=add");
        exit();
    }
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = (int)$_POST['id_kategori'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    if (empty($nama)) {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        mysqli_query($conn, "UPDATE kategori SET nama_kategori = '$nama' WHERE id_kategori = $id");
        header("Location: kategori.php?success=edit");
        exit();
    }
}

$result = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori - Perpustakaan</title>
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
            <h2 class="fw-bold mb-0">Data Kategori</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="window.print()" title="Print">
                    <i class="bi bi-printer"></i>
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
                </button>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                    if ($_GET['success'] == 'add') echo "Kategori berhasil ditambahkan!";
                    elseif ($_GET['success'] == 'edit') echo "Kategori berhasil diperbarui!";
                    elseif ($_GET['success'] == 'delete') echo "Kategori berhasil dihapus!";
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
                                <th>Nama Kategori</th>
                                <th>Jumlah Buku</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)):
                                    $jml = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku WHERE id_kategori = " . $row['id_kategori']))['total'];
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                    <td><span class="badge bg-primary"><?= $jml ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_kategori'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="kategori.php?delete=<?= $row['id_kategori'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="modalEdit<?= $row['id_kategori'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Kategori</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_kategori" value="<?= $row['id_kategori'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Kategori</label>
                                                                <input type="text" class="form-control" name="nama_kategori" value="<?= htmlspecialchars($row['nama_kategori']) ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data kategori</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" name="nama_kategori" placeholder="Masukkan nama kategori" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
