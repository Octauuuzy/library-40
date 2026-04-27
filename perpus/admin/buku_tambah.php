<?php
$active = 'buku';
require_once 'auth_check.php';
require_once 'cover_helper.php';

$error = '';

$kategori_result = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = (int)$_POST['id_kategori'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok = (int)$_POST['stok'];

    $query = "INSERT INTO buku (id_kategori, judul, penulis, tahun_terbit, deskripsi, stok) 
              VALUES ($id_kategori, '$judul', '$penulis', '$tahun_terbit', '$deskripsi', $stok)";
    if (mysqli_query($conn, $query)) {
        $new_id = mysqli_insert_id($conn);

        // Upload cover ke folder perpus/cover/ dengan nama id_buku.ext
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
            $result = saveCover($new_id, $_FILES['cover']);
            if ($result !== true) {
                $error = $result;
            }
        }

        if (empty($error)) {
            header("Location: buku.php?success=add");
            exit();
        }
    } else {
        $error = "Gagal menambahkan buku: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .main-content { margin-left: 260px; padding: 20px; }
        .cover-preview { max-width: 150px; max-height: 200px; object-fit: cover; border-radius: 8px; display: none; }
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
            <h2 class="fw-bold mb-0">Tambah Buku</h2>
            <a href="buku.php" class="btn btn-secondary">
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
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="judul" class="form-label">Judul Buku</label>
                                    <input type="text" class="form-control" id="judul" name="judul" required value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="penulis" class="form-label">Penulis</label>
                                    <input type="text" class="form-control" id="penulis" name="penulis" required value="<?= isset($_POST['penulis']) ? htmlspecialchars($_POST['penulis']) : '' ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="id_kategori" name="id_kategori" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php while ($kat = mysqli_fetch_assoc($kategori_result)): ?>
                                            <option value="<?= $kat['id_kategori'] ?>" <?= (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                    <input type="text" class="form-control" id="tahun_terbit" name="tahun_terbit" required value="<?= isset($_POST['tahun_terbit']) ? htmlspecialchars($_POST['tahun_terbit']) : '' ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="stok" class="form-label">Stok</label>
                                    <input type="number" class="form-control" id="stok" name="stok" min="0" required value="<?= isset($_POST['stok']) ? (int)$_POST['stok'] : 1 ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cover" class="form-label">Cover Buku</label>
                                <input type="file" class="form-control" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" onchange="previewCover(this)">
                                <small class="text-muted">Format: JPG, PNG, WEBP. Maks: 2MB</small>
                            </div>
                            <img id="coverPreview" class="cover-preview mt-2" alt="Preview">
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
    <script>
        function previewCover(input) {
            const preview = document.getElementById('coverPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
