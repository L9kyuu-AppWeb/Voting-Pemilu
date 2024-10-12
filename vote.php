<?php
session_start(); // Mulai session
include 'koneksi.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$candidate_id = $_POST['candidate_id'] ?? null;
$email = $_SESSION['email']; // Ambil email dari session

if ($candidate_id && $email) {
    try {
        // Cek apakah peserta sudah memilih
        $stmt = $pdo->prepare('SELECT * FROM tb_participants WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participant && !$participant['has_voted']) {
            // Catat vote peserta
            $stmt = $pdo->prepare('INSERT INTO tb_votes (participant_id, candidate_id) VALUES (:participant_id, :candidate_id)');
            $stmt->execute(['participant_id' => $participant['id'], 'candidate_id' => $candidate_id]);
            
            // Update status peserta telah memilih
            $stmt = $pdo->prepare('UPDATE tb_participants SET has_voted = 1 WHERE id = :id');
            $stmt->execute(['id' => $participant['id']]);
            
            echo json_encode(['success' => true, 'message' => 'Vote successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'You have already voted or participant not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Candidate not provided.']);
}
