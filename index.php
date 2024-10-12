<?php
session_start(); // Memulai session
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if ($email) {
        try {
            $stmt = $pdo->prepare('SELECT * FROM tb_participants WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($participant) {
                if ($participant['has_voted']) {
                    $error = 'You have already voted.';
                } else {
                    // Login berhasil, simpan email di session
                    $_SESSION['email'] = $email;
                    header('Location: voting.php'); // Redirect ke voting.php
                    exit;
                }
            } else {
                $error = 'Email not found in the system.';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter an email address.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-purple-100">
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-purple-600 mb-4">Login</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            <button type="submit" class="bg-purple-500 text-white py-2 px-4 rounded w-full">Login</button>
        </form>
    </div>

    <!-- Enlarged Poster Image Section -->
    <div class="max-w-2xl mx-auto mt-6 p-6 bg-white rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-center text-purple-600 mb-4">Event Poster</h3>
        <div class="flex justify-center">
            <img src="gambar.jpeg" alt="Event Poster" class="rounded-lg shadow-lg w-full max-h-[500px] object-contain">
        </div>
    </div>
</body>
</html>


