<?php
// Sidebar component for admin pages
// $active variable should be set before including this file
?>
<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark sidebar" style="width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; overflow-y: auto;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <img src="../assets/logo.png" alt="Logo" style="width: 36px; height: 36px; object-fit: contain;" class="me-2">
        <span class="fs-5 fw-bold">Perpustakaan 40</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white <?= (isset($active) && $active == 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="anggota.php" class="nav-link text-white <?= (isset($active) && $active == 'anggota') ? 'active' : '' ?>">
                <i class="bi bi-people me-2"></i>Anggota
            </a>
        </li>
        <li class="nav-item">
            <a href="kategori.php" class="nav-link text-white <?= (isset($active) && $active == 'kategori') ? 'active' : '' ?>">
                <i class="bi bi-tags me-2"></i>Kategori
            </a>
        </li>
        <li class="nav-item">
            <a href="buku.php" class="nav-link text-white <?= (isset($active) && $active == 'buku') ? 'active' : '' ?>">
                <i class="bi bi-journal-bookmark me-2"></i>Buku
            </a>
        </li>
        <li class="nav-item">
            <a href="peminjaman.php" class="nav-link text-white <?= (isset($active) && $active == 'peminjaman') ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right me-2"></i>Peminjaman
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2" style="font-size: 1.3rem;"></i>
            <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</div>
