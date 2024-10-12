<?php
// Database connection
include 'koneksi.php';

$email = $_POST['email'] ?? '';

if ($email) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM tb_participants WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($participant) {
            if ($participant['has_voted']) {
                echo json_encode(['success' => false, 'message' => 'You have already voted.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Login successful.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Email not found in the system.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Please enter an email address.']);
}

