<?php
include '../koneksi.php';

$name = $_POST['name'];
$photo = $_POST['photo'];
$vision = $_POST['vision'];
$mission = $_POST['mission'];

$stmt = $pdo->prepare('INSERT INTO tb_candidates (name, photo, vision, mission) VALUES (:name, :photo, :vision, :mission)');
$stmt->execute(['name' => $name, 'photo' => $photo, 'vision' => $vision, 'mission' => $mission]);

echo json_encode(['message' => 'Candidate added successfully.']);
?>
