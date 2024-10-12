<?php
include '../koneksi.php';

$id = $_POST['id'] ?? null;

if ($id) {
    // Ambil informasi kandidat untuk mendapatkan nama file gambar
    $stmt = $pdo->prepare('SELECT photo FROM tb_candidates WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($candidate) {
        $photoPath = '../' . $candidate['photo']; // Path lengkap ke foto

        // Hapus file gambar jika ada
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }

        // Hapus kandidat dari database
        $deleteStmt = $pdo->prepare('DELETE FROM tb_candidates WHERE id = :id');
        if ($deleteStmt->execute(['id' => $id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting candidate.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Candidate not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No candidate ID provided.']);
}
?>
