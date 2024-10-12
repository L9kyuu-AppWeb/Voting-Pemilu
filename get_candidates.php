<?php
// Database connection
include 'koneksi.php';

$stmt = $pdo->query('SELECT * FROM tb_candidates');
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($candidates) {
    echo json_encode(['success' => true, 'candidates' => $candidates]);
} else {
    echo json_encode(['success' => false, 'message' => 'No candidates found.']);
}
