<?php
require_once 'auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit();
}

$id_buku = isset($_POST['id_buku']) ? (int)$_POST['id_buku'] : 0;
$id_user = (int)$_SESSION['user_id'];

if ($id_buku <= 0) {
    echo json_encode(['success' => false]);
    exit();
}

// Check if already starred
$cek = mysqli_query($conn, "SELECT id_favorit FROM favorit WHERE id_user = $id_user AND id_buku = $id_buku");

if (mysqli_num_rows($cek) > 0) {
    // Remove star
    mysqli_query($conn, "DELETE FROM favorit WHERE id_user = $id_user AND id_buku = $id_buku");
    $starred = false;
} else {
    // Add star
    mysqli_query($conn, "INSERT INTO favorit (id_user, id_buku) VALUES ($id_user, $id_buku)");
    $starred = true;
}

// Get new count
$count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM favorit WHERE id_buku = $id_buku"))['total'];

echo json_encode(['success' => true, 'starred' => $starred, 'count' => (int)$count]);
