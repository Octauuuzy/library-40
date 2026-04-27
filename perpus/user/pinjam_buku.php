<?php
require_once 'auth_check.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Method not allowed';
    echo json_encode($response);
    exit();
}

$id_user = (int)$_SESSION['user_id'];
$id_buku = isset($_POST['id_buku']) ? (int)$_POST['id_buku'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($id_buku <= 0) {
    $response['message'] = 'ID buku tidak valid';
    echo json_encode($response);
    exit();
}

// Check if book exists
$buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = $id_buku"));
if (!$buku) {
    $response['message'] = 'Buku tidak ditemukan';
    echo json_encode($response);
    exit();
}

if ($action === 'pinjam') {
    $durasi = isset($_POST['durasi']) ? (int)$_POST['durasi'] : 7;

    // Validate durasi (1-30 days)
    if ($durasi < 1 || $durasi > 30) {
        $response['message'] = 'Durasi peminjaman harus antara 1-30 hari';
        echo json_encode($response);
        exit();
    }

    // Check if user already borrowed this book
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_user = $id_user AND id_buku = $id_buku AND status_peminjaman = 'dipinjam'"));
    if ($existing) {
        $response['message'] = 'Anda sudah meminjam buku ini';
        echo json_encode($response);
        exit();
    }

    // Check stock availability
    $active_borrows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = $id_buku AND status_peminjaman = 'dipinjam'"))['total'];
    $sisa_stok = (int)$buku['stok'] - (int)$active_borrows;

    if ($sisa_stok <= 0) {
        $response['message'] = 'Stok buku habis';
        echo json_encode($response);
        exit();
    }

    // Create peminjaman record
    $tanggal_peminjaman = date('Y-m-d');
    $tanggal_pengembalian = date('Y-m-d', strtotime("+$durasi days"));

    $query = "INSERT INTO peminjaman (id_user, id_buku, tanggal_peminjaman, tanggal_pengembalian, status_peminjaman)
              VALUES ($id_user, $id_buku, '$tanggal_peminjaman', '$tanggal_pengembalian', 'dipinjam')";

    if (mysqli_query($conn, $query)) {
        $response['success'] = true;
        $response['message'] = 'Buku berhasil dipinjam';
        $response['tanggal_kembali'] = date('d/m/Y', strtotime($tanggal_pengembalian));
    } else {
        $response['message'] = 'Gagal meminjam buku: ' . mysqli_error($conn);
    }

} elseif ($action === 'kembalikan') {
    // Find the active borrow record
    $peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_user = $id_user AND id_buku = $id_buku AND status_peminjaman = 'dipinjam'"));

    if (!$peminjaman) {
        $response['message'] = 'Tidak ada peminjaman aktif untuk buku ini';
        echo json_encode($response);
        exit();
    }

    // Calculate denda if late
    $setting_toleransi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'toleransi_hari'"));
    $setting_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM setting WHERE nama = 'denda_per_hari'"));
    $toleransi = (int)($setting_toleransi['nilai'] ?? 0);
    $denda_per_hari = (int)($setting_denda['nilai'] ?? 0);

    $tgl_kembali = new DateTime($peminjaman['tanggal_pengembalian']);
    $hari_ini = new DateTime(date('Y-m-d'));
    $selisih = (int)$hari_ini->diff($tgl_kembali)->format('%r%a');
    $hari_telat = -$selisih - $toleransi;
    $denda = ($hari_telat > 0) ? $hari_telat * $denda_per_hari : 0;

    $tanggal_dikembalikan = date('Y-m-d');
    $id_peminjaman = (int)$peminjaman['id_peminjaman'];

    $query = "UPDATE peminjaman SET status_peminjaman = 'dikembalikan', tanggal_dikembalikan = '$tanggal_dikembalikan', denda = $denda WHERE id_peminjaman = $id_peminjaman";

    if (mysqli_query($conn, $query)) {
        $response['success'] = true;
        $response['message'] = 'Buku berhasil dikembalikan';
        $response['denda'] = $denda;
    } else {
        $response['message'] = 'Gagal mengembalikan buku: ' . mysqli_error($conn);
    }

} else {
    $response['message'] = 'Action tidak valid';
}

echo json_encode($response);
