<?php
include '../koneksi.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM tb_candidates WHERE id = ?');
    $stmt->execute([$id]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($candidate);
} else {
    echo json_encode(['error' => 'No ID provided.']);
}
?>
