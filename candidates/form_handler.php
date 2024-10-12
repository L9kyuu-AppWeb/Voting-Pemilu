<?php
include '../koneksi.php';

$id = $_POST['id'] ?? '';
$name = $_POST['name'];
$vision = $_POST['vision'];
$mission = $_POST['mission'];
$photo = $_FILES['photo'] ?? null;
$photoPath = '';

// Check if photo is uploaded
if ($photo && $photo['tmp_name']) {
    $uploadDir = 'images/'; // Path ke direktori images
    $photoPath = 'images/' . time() . '_' . basename($photo['name']); // Benar hanya 'images/'

    // Cek apakah file berhasil dipindahkan
    if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
        // File berhasil di-upload
    } else {
        // Tampilkan pesan kesalahan jika upload gagal
        echo "Failed to upload image.";
        exit;
    }
}

// Insert or update candidate
if ($id) {
    // Update candidate
    if ($photoPath) {
        $stmt = $pdo->prepare('UPDATE tb_candidates SET name = :name, vision = :vision, mission = :mission, photo = :photo WHERE id = :id');
        $stmt->execute(['name' => $name, 'vision' => $vision, 'mission' => $mission, 'photo' => $photoPath, 'id' => $id]);
    } else {
        $stmt = $pdo->prepare('UPDATE tb_candidates SET name = :name, vision = :vision, mission = :mission WHERE id = :id');
        $stmt->execute(['name' => $name, 'vision' => $vision, 'mission' => $mission, 'id' => $id]);
    }
} else {
    // Add new candidate
    if ($photoPath) {
        $stmt = $pdo->prepare('INSERT INTO tb_candidates (name, vision, mission, photo) VALUES (:name, :vision, :mission, :photo)');
        $stmt->execute(['name' => $name, 'vision' => $vision, 'mission' => $mission, 'photo' => $photoPath]);
    } else {
        // Tampilkan pesan kesalahan jika tidak ada gambar
        echo "Image is required for new candidates.";
        exit;
    }
}

header('Location: index.php');
