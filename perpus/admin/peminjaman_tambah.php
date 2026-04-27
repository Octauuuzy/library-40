<?php
$active = 'peminjaman';
require_once 'auth_check.php';

$error = '';

// Get anggota & buku untuk dropdown
$anggota_result = mysqli_query($conn, "SELECT * FROM user WHERE level = 'anggota' ORDER BY nama ASC");
$buku_result = mysqli_query($conn, "SELECT * FROM buku ORDER BY judul ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = (int)$_POST['id_user'];
    $id_buku = (int)$_POST['id_buku'];
    $tanggal_peminjaman = mysqli_real_escape_string($conn, $_POST['tanggal_peminjaman']);
    $tanggal_pengembalian = mysqli_real_escape_string($conn, $_POST['tanggal_pengembalian']);

    // Cek apakah buku sedang dipinjam
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = $id_buku AND status_peminjaman = 'dipinjam'"));
    if ($cek['total'] > 0) {
        $error = "Buku ini sedang dipinjam oleh orang lain!";
    } else {
        $query = "INSERT INTO peminjaman (id_user, id_buku, tanggal_peminjaman, tanggal_pengembalian, status_peminjaman) 
                  VALUES ($id_user, $id_buku, '$tanggal_peminjaman', '$tanggal_pengembalian', 'dipinjam')";
        if (mysqli_query($conn, $query)) {
            header("Location: peminjaman.php?success=add");
            exit();
        } else {
            $error = "Gagal menambahkan peminjaman: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .main-content { margin-left: 260px; padding: 20px; }
        @media (max-width: 768px) {
            .sidebar { width: 100% !important; position: relative !important; min-height: auto !important; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Tambah Peminjaman</h2>
            <a href="peminjaman.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_user" class="form-label">Peminjam (Anggota)</label>
                            <select class="form-select" id="id_user" name="id_user" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php while ($row = mysqli_fetch_assoc($anggota_result)): ?>
                                    <option value="<?= $row['id_user'] ?>" <?= (isset($_POST['id_user']) && $_POST['id_user'] == $row['id_user']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['nama']) ?> (<?= htmlspecialchars($row['username']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_buku" class="form-label">Buku</label>
                            <select class="form-select" id="id_buku" name="id_buku" required>
                                <option value="">-- Pilih Buku --</option>
                                <?php while ($row = mysqli_fetch_assoc($buku_result)): ?>
                                    <option value="<?= $row['id_buku'] ?>" <?= (isset($_POST['id_buku']) && $_POST['id_buku'] == $row['id_buku']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['judul']) ?> - <?= htmlspecialchars($row['penulis']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                            <input type="date" class="form-control" id="tanggal_peminjaman" name="tanggal_peminjaman" required value="<?= isset($_POST['tanggal_peminjaman']) ? htmlspecialchars($_POST['tanggal_peminjaman']) : date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="tanggal_pengembalian" name="tanggal_pengembalian" required value="<?= isset($_POST['tanggal_pengembalian']) ? htmlspecialchars($_POST['tanggal_pengembalian']) : date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
