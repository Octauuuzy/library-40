<?php
require_once __DIR__ . '/../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user is anggota
if ($_SESSION['level'] != 'anggota') {
    header("Location: ../login.php");
    exit();
}
?>
