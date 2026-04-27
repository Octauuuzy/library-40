<?php
$active = 'peminjaman';
require_once 'auth_check.php';

// Load setting denda
$setting_toleransi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'toleransi_hari'"));
$setting_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'denda_per_hari'"));
$toleransi_hari = $setting_toleransi ? (int)$setting_toleransi['nilai'] : 0;
$denda_per_hari = $setting_denda ? (int)$setting_denda['nilai'] : 5000;

// Handle save setting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_setting'])) {
    $tol = (int)$_POST['toleransi_hari'];
    $dpd = (int)$_POST['denda_per_hari'];
    mysqli_query($conn, "UPDATE setting SET nilai = '$tol' WHERE nama = 'toleransi_hari'");
    mysqli_query($conn, "UPDATE setting SET nilai = '$dpd' WHERE nama = 'denda_per_hari'");
    header("Location: peminjaman.php?success=setting");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman = $id");
    header("Location: peminjaman.php?success=delete");
    exit();
}

// Handle kembalikan
if (isset($_GET['kembalikan'])) {
    $id = (int)$_GET['kembalikan'];
    $pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman = $id"));
    if ($pinjam) {
        $tgl_kembali_seharusnya = $pinjam['tanggal_pengembalian'];
        $tgl_dikembalikan = date('Y-m-d');
        // Hitung denda
        $selisih = (strtotime($tgl_dikembalikan) - strtotime($tgl_kembali_seharusnya)) / 86400;
        $hari_terlambat = max(0, $selisih - $toleransi_hari);
        $denda = (int)$hari_terlambat * $denda_per_hari;
        $tgl_safe = mysqli_real_escape_string($conn, $tgl_dikembalikan);
        mysqli_query($conn, "UPDATE peminjaman SET status_peminjaman='dikembalikan', tanggal_dikembalikan='$tgl_safe', denda=$denda WHERE id_peminjaman = $id");
    }
    header("Location: peminjaman.php?success=kembalikan");
    exit();
}

// Filter status
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$where = '';
if ($filter == 'dipinjam') {
    $where = "WHERE p.status_peminjaman = 'dipinjam'";
} elseif ($filter == 'dikembalikan') {
    $where = "WHERE p.status_peminjaman = 'dikembalikan'";
}

$query = "SELECT p.*, u.nama, b.judul 
          FROM peminjaman p 
          JOIN user u ON p.id_user = u.id_user 
          JOIN buku b ON p.id_buku = b.id_buku 
          $where
          ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman - Perpustakaan</title>
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
            .alert, .btn-group { display: none !important; }
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
            <h2 class="fw-bold mb-0">Data Peminjaman</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalSetting" title="Pengaturan Denda">
                    <i class="bi bi-gear-fill"></i>
                </button>
                <button class="btn btn-outline-secondary" onclick="window.print()" title="Print">
                    <i class="bi bi-printer"></i>
                </button>
                <a href="peminjaman_tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Peminjaman
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                    if ($_GET['success'] == 'add') echo "Peminjaman berhasil ditambahkan!";
                    elseif ($_GET['success'] == 'edit') echo "Peminjaman berhasil diperbarui!";
                    elseif ($_GET['success'] == 'delete') echo "Peminjaman berhasil dihapus!";
                    elseif ($_GET['success'] == 'kembalikan') echo "Buku berhasil dikembalikan!";
                    elseif ($_GET['success'] == 'setting') echo "Pengaturan denda berhasil disimpan!";
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

        <!-- Filter -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="peminjaman.php" class="btn btn-outline-secondary <?= $filter == '' ? 'active' : '' ?>">Semua</a>
                <a href="peminjaman.php?filter=dipinjam" class="btn btn-outline-warning <?= $filter == 'dipinjam' ? 'active' : '' ?>">Dipinjam</a>
                <a href="peminjaman.php?filter=dikembalikan" class="btn btn-outline-success <?= $filter == 'dikembalikan' ? 'active' : '' ?>">Dikembalikan</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Tgl Dikembalikan</th>
                                <th>Denda</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['judul']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_peminjaman'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_pengembalian'])) ?></td>
                                    <td>
                                        <?php if ($row['tanggal_dikembalikan']): ?>
                                            <?= date('d/m/Y', strtotime($row['tanggal_dikembalikan'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['denda'] > 0): ?>
                                            <span class="text-danger fw-bold">Rp<?= number_format($row['denda'], 0, ',', '.') ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status_peminjaman'] == 'dipinjam'): ?>
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status_peminjaman'] == 'dipinjam'): ?>
                                            <a href="peminjaman.php?kembalikan=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pengembalian buku?')">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="peminjaman_edit.php?id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="peminjaman.php?delete=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada data peminjaman</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Setting Denda -->
    <div class="modal fade" id="modalSetting" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Pengaturan Denda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Toleransi Hari</label>
                            <input type="number" class="form-control" name="toleransi_hari" min="0" value="<?= $toleransi_hari ?>" required>
                            <small class="text-muted">Jumlah hari setelah batas kembali sebelum denda berlaku. Set 0 jika langsung didenda.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Denda Per Hari (Rp)</label>
                            <input type="number" class="form-control" name="denda_per_hari" min="0" value="<?= $denda_per_hari ?>" required>
                            <small class="text-muted">Jumlah denda per hari keterlambatan.</small>
                        </div>
                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                Contoh: Toleransi <strong>2 hari</strong>, denda <strong>Rp30.000/hari</strong> — jika terlambat 5 hari, denda = (5 - 2) x Rp30.000 = <strong>Rp90.000</strong>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="save_setting" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
