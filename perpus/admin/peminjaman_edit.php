<?php
$active = 'peminjaman';
require_once 'auth_check.php';

$error = '';

// Load setting denda
$setting_toleransi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'toleransi_hari'"));
$setting_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'denda_per_hari'"));
$toleransi_hari = $setting_toleransi ? (int)$setting_toleransi['nilai'] : 0;
$denda_per_hari = $setting_denda ? (int)$setting_denda['nilai'] : 5000;

if (!isset($_GET['id'])) {
    header("Location: peminjaman.php");
    exit();
}

$id = (int)$_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman = $id"));

if (!$data) {
    header("Location: peminjaman.php");
    exit();
}

$anggota_result = mysqli_query($conn, "SELECT * FROM user WHERE level = 'anggota' ORDER BY nama ASC");
$buku_result = mysqli_query($conn, "SELECT * FROM buku ORDER BY judul ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = (int)$_POST['id_user'];
    $id_buku = (int)$_POST['id_buku'];
    $tanggal_peminjaman = mysqli_real_escape_string($conn, $_POST['tanggal_peminjaman']);
    $tanggal_pengembalian = mysqli_real_escape_string($conn, $_POST['tanggal_pengembalian']);
    $status = mysqli_real_escape_string($conn, $_POST['status_peminjaman']);
    $tanggal_dikembalikan = !empty($_POST['tanggal_dikembalikan']) ? mysqli_real_escape_string($conn, $_POST['tanggal_dikembalikan']) : null;

    // Hitung denda otomatis
    $denda = 0;
    if ($status == 'dikembalikan' && $tanggal_dikembalikan) {
        $selisih = (strtotime($tanggal_dikembalikan) - strtotime($tanggal_pengembalian)) / 86400;
        $hari_terlambat = max(0, $selisih - $toleransi_hari);
        $denda = (int)$hari_terlambat * $denda_per_hari;
    }

    // Cek apakah buku sedang dipinjam orang lain
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = $id_buku AND status_peminjaman = 'dipinjam' AND id_peminjaman != $id"));
    if ($cek['total'] > 0 && $status == 'dipinjam') {
        $error = "Buku ini sedang dipinjam oleh orang lain!";
    } else {
        $tgl_dk_sql = $tanggal_dikembalikan ? "'$tanggal_dikembalikan'" : "NULL";
        $query = "UPDATE peminjaman SET id_user=$id_user, id_buku=$id_buku, 
                  tanggal_peminjaman='$tanggal_peminjaman', tanggal_pengembalian='$tanggal_pengembalian',
                  tanggal_dikembalikan=$tgl_dk_sql, denda=$denda,
                  status_peminjaman='$status' WHERE id_peminjaman = $id";
        if (mysqli_query($conn, $query)) {
            header("Location: peminjaman.php?success=edit");
            exit();
        } else {
            $error = "Gagal memperbarui peminjaman: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman - Perpustakaan</title>
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
            <h2 class="fw-bold mb-0">Edit Peminjaman</h2>
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
                                    <option value="<?= $row['id_user'] ?>" <?= ($data['id_user'] == $row['id_user']) ? 'selected' : '' ?>>
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
                                    <option value="<?= $row['id_buku'] ?>" <?= ($data['id_buku'] == $row['id_buku']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['judul']) ?> - <?= htmlspecialchars($row['penulis']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                            <input type="date" class="form-control" id="tanggal_peminjaman" name="tanggal_peminjaman" required value="<?= $data['tanggal_peminjaman'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_pengembalian" class="form-label">Batas Pengembalian</label>
                            <input type="date" class="form-control" id="tanggal_pengembalian" name="tanggal_pengembalian" required value="<?= $data['tanggal_pengembalian'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status_peminjaman" class="form-label">Status</label>
                            <select class="form-select" id="status_peminjaman" name="status_peminjaman" required onchange="toggleTglDikembalikan(this)">
                                <option value="dipinjam" <?= ($data['status_peminjaman'] == 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                                <option value="dikembalikan" <?= ($data['status_peminjaman'] == 'dikembalikan') ? 'selected' : '' ?>>Dikembalikan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="rowDikembalikan" style="<?= $data['status_peminjaman'] == 'dipinjam' ? 'display:none' : '' ?>">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_dikembalikan" class="form-label">Tanggal Dikembalikan</label>
                            <input type="date" class="form-control" id="tanggal_dikembalikan" name="tanggal_dikembalikan" value="<?= $data['tanggal_dikembalikan'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Denda Saat Ini</label>
                            <div class="form-control bg-light">
                                <?php if ($data['denda'] > 0): ?>
                                    <span class="text-danger fw-bold">Rp<?= number_format($data['denda'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada denda</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">Denda akan dihitung ulang otomatis saat disimpan.</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleTglDikembalikan(select) {
            const row = document.getElementById('rowDikembalikan');
            const input = document.getElementById('tanggal_dikembalikan');
            if (select.value === 'dikembalikan') {
                row.style.display = '';
                if (!input.value) input.value = new Date().toISOString().split('T')[0];
            } else {
                row.style.display = 'none';
                input.value = '';
            }
        }
    </script>
</body>
</html>
