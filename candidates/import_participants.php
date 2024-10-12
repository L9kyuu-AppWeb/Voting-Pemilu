<?php
require_once '../koneksi.php';

// Import data functionality
if (isset($_POST['import'])) {
    $fileName = $_FILES['file']['tmp_name'];

    if ($_FILES['file']['size'] > 0) {
        $file = fopen($fileName, 'r');

        // Skip the first line if your CSV has headers
        fgetcsv($file);

        while (($row = fgetcsv($file, 1000, ';')) !== FALSE) {
            $name = $row[0];
            $email = $row[1];

            // Insert into the database
            $stmt = $pdo->prepare("INSERT INTO tb_participants (name, email) VALUES (:name, :email)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);

            try {
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error importing data: " . $e->getMessage();
            }
        }

        fclose($file);
        echo "Data imported successfully!";
    }
}

// Download template functionality
if (isset($_GET['download_template'])) {
    $file = 'participants_template.csv'; // Path to your CSV template
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=' . basename($file));
    readfile($file);
    exit();
}

// Delete participant functionality
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM tb_participants WHERE id = :id");
    $stmt->bindParam(':id', $id);

    try {
        $stmt->execute();
        echo "Participant deleted successfully!";
    } catch (PDOException $e) {
        echo "Error deleting participant: " . $e->getMessage();
    }
}

// Fetch participants for display
$stmt = $pdo->query("SELECT * FROM tb_participants");
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count participants who have voted and who have not
$votedCount = 0;
$notVotedCount = 0;

foreach ($participants as $participant) {
    if ($participant['has_voted']) {
        $votedCount++;
    } else {
        $notVotedCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Participants</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function refreshPage() {
            window.location.reload();
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-purple-600 mb-4">Import Participants</h2>

        <!-- Download Template Button -->
        <a href="import_participants.php?download_template=true" class="bg-green-500 text-white py-2 px-4 rounded mb-4 inline-block text-center">Download Template CSV</a>

        <form action="import_participants.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Choose CSV File</label>
                <input type="file" name="file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            <button type="submit" name="import" class="bg-purple-500 text-white py-2 px-4 rounded w-full">Import</button>
        </form>

        <!-- Refresh Button -->
        <button onclick="refreshPage()" class="bg-blue-500 text-white py-2 px-4 rounded mt-4 mb-4">Refresh Page</button>

        <h3 class="text-xl font-bold text-purple-600 mt-6 mb-4">Participants List</h3>

        <!-- Summary of Votes -->
        <div class="mb-4">
            <p class="text-gray-700">Participants who have voted: <strong><?php echo $votedCount; ?></strong></p>
            <p class="text-gray-700">Participants who have not voted: <strong><?php echo $notVotedCount; ?></strong></p>
        </div>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="border-b-2 border-gray-300 px-4 py-2">ID</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Name</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Email</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Has Voted</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                <tr>
                    <td class="border-b border-gray-300 px-4 py-2"><?php echo htmlspecialchars($participant['id']); ?></td>
                    <td class="border-b border-gray-300 px-4 py-2"><?php echo htmlspecialchars($participant['name']); ?></td>
                    <td class="border-b border-gray-300 px-4 py-2"><?php echo htmlspecialchars($participant['email']); ?></td>
                    <td class="border-b border-gray-300 px-4 py-2"><?php echo $participant['has_voted'] ? 'Yes' : 'No'; ?></td>
                    <td class="border-b border-gray-300 px-4 py-2">
                        <a href="import_participants.php?delete_id=<?php echo $participant['id']; ?>" class="text-red-500 hover:text-red-700">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
