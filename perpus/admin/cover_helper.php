<?php
// Helper: cari file cover berdasarkan id_buku di folder perpus/cover/
function getCover($id_buku) {
    $dir = __DIR__ . '/../cover/';
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($extensions as $ext) {
        $file = $dir . $id_buku . '.' . $ext;
        if (file_exists($file)) {
            return $id_buku . '.' . $ext;
        }
    }
    return null;
}

function saveCover($id_buku, $file) {
    $dir = __DIR__ . '/../cover/';
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($ext, $allowed)) {
        return "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau WEBP.";
    }
    if ($file['size'] > $max_size) {
        return "Ukuran file terlalu besar! Maksimal 2MB.";
    }

    // Hapus cover lama dulu
    deleteCover($id_buku);

    // Simpan cover baru: namafile = id_buku.ext
    $target = $dir . $id_buku . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return "Gagal mengupload cover!";
    }
    return true;
}

function deleteCover($id_buku) {
    $dir = __DIR__ . '/../cover/';
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    foreach ($extensions as $ext) {
        $file = $dir . $id_buku . '.' . $ext;
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
?>
