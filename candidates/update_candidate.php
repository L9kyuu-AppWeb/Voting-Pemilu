<?php
include '../koneksi.php';

$id = $_POST['id'];
$name = $_POST['name'];
$photo = $_POST['photo'];
$vision = $_POST['vision'];
$mission = $_POST['mission'];

$stmt = $pdo->prepare('UPDATE tb_candidates SET name = :name, photo = :photo, vision = :vision, mission = :mission WHERE id = :id');
$stmt->execute(['name' => $name, 'photo' => $photo, 'vision' => $vision, 'mission' => $mission, 'id' => $id]);

echo json_encode(['message' => 'Candidate updated successfully.']);
?>
