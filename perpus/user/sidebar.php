<?php
// Sidebar component for user/anggota pages
// $active variable should be set before including this file
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id_user = " . (int)$_SESSION['user_id']));
?>
<div class="d-flex flex-column flex-shrink-0 sidebar-user" style="width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; overflow-y: auto; background: #fff; border-right: 1px solid #e9ecef;">
    <div class="p-3">
        <a href="dashboard.php" class="d-flex align-items-center text-decoration-none mb-2">
            <img src="../assets/logo.png" alt="Logo" style="width: 36px; height: 36px; object-fit: contain;" class="me-2">
            <div>
                <span class="fw-bold text-dark" style="font-size: 1rem;">Perpustakaan 40</span><br>
                <small class="text-muted" style="font-size: 0.75rem;">Portal Anggota</small>
            </div>
        </a>
    </div>

    <div class="px-3">
        <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Utama</small>
    </div>
    <ul class="nav flex-column px-2 mt-1">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link rounded-3 <?= (isset($active) && $active == 'beranda') ? 'active-sidebar' : 'text-dark' ?>">
                <i class="bi bi-house-door me-2"></i>Beranda
            </a>
        </li>
        <li class="nav-item">
            <a href="koleksi.php" class="nav-link rounded-3 <?= (isset($active) && $active == 'koleksi') ? 'active-sidebar' : 'text-dark' ?>">
                <i class="bi bi-collection me-2"></i>Koleksi
            </a>
        </li>
        <li class="nav-item">
            <a href="favorit.php" class="nav-link rounded-3 <?= (isset($active) && $active == 'favorit') ? 'active-sidebar' : 'text-dark' ?>">
                <i class="bi bi-heart me-2"></i>Favorit
            </a>
        </li>
        <li class="nav-item">
            <a href="ebook.php" class="nav-link rounded-3 <?= (isset($active) && $active == 'ebook') ? 'active-sidebar' : 'text-dark' ?>">
                <i class="bi bi-file-earmark-text me-2"></i>E-book
            </a>
        </li>
    </ul>

    <div class="mt-auto p-3 border-top">
        <div class="d-flex align-items-center mb-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 600;">
                <?= strtoupper(substr($user_data['nama'], 0, 2)) ?>
            </div>
            <div style="line-height: 1.2;">
                <span class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($user_data['nama']) ?></span><br>
                <small class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($user_data['email']) ?></small>
            </div>
        </div>
        <a href="../logout.php" class="btn btn-outline-dark btn-sm w-100 mt-1">
            <i class="bi bi-box-arrow-left me-1"></i>Keluar
        </a>
    </div>
</div>

<style>
.sidebar-user .nav-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    transition: all 0.15s;
}
.sidebar-user .nav-link:hover {
    background: #f0f0f0;
}
.sidebar-user .active-sidebar {
    background: rgba(2, 0, 36, 0.85) !important;
    color: #fff !important;
}
.sidebar-user .active-sidebar:hover {
    background: rgba(2, 0, 36, 0.95) !important;
}
</style>
