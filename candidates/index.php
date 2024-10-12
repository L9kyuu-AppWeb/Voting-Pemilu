<?php
session_start(); // Memulai session
include '../koneksi.php';

if (isset($_GET['amankan$123']) && $_GET['amankan$123'] === "l9kyuu") {
    @$_SESSION['amankan'] = 'Y';
    
    // Redirect to the same page (reload)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Ensure no further code is executed after the redirect
}

// Check if the session variable 'amankan' is set and equals to 'Y'
if (!isset($_SESSION['amankan']) || $_SESSION['amankan'] != 'Y') {
    // Display an error message
    echo "<p style='color:red;'>Error: You must enter the correct link.</p>";
    exit(); // Stop further execution if the session condition is not met
}


// Fetch candidates with their vote counts
$stmt = $pdo->query('
    SELECT c.*, 
           (SELECT COUNT(*) FROM tb_votes WHERE tb_votes.candidate_id = c.id) AS vote_count 
    FROM tb_candidates c
');
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Find the candidate with the most votes
$maxVotes = 0;
$winnerId = null;
foreach ($candidates as $candidate) {
    if ($candidate['vote_count'] > $maxVotes) {
        $maxVotes = $candidate['vote_count'];
        $winnerId = $candidate['id'];
    }
}

// Handle SQL download request
if (isset($_POST['download_sql'])) {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

    // Use mysqldump to generate SQL backup (assuming mysqldump is available on the server)
    $command = "mysqldump --user={$dbuser} --password={$dbpass} --host={$dbhost} {$dbname} > /tmp/{$filename}";
    exec($command);

    // Serve the file for download
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename={$filename}");
    readfile("/tmp/{$filename}");
    exit();
}

// Handle reset votes request
if (isset($_POST['reset_votes'])) {
    // Reset votes in tb_votes table
    $pdo->exec('DELETE FROM tb_votes');

    // Reset participants' vote status
    $pdo->exec('UPDATE tb_participants SET has_voted = 0');

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold text-center mb-8">Manage Candidates</h1>
        <!-- Add Candidate Button -->
        <div class="flex justify-end mb-4">
            <button onclick="openForm('add')" class="bg-blue-500 text-white px-4 py-2 rounded">Add Candidate</button>
        </div>

        <!-- SQL Download and Reset Voting Buttons -->
        <div class="flex justify-end mb-4 space-x-4">
            <form method="POST" class="inline-block">
                <button type="submit" name="download_sql" class="bg-green-500 text-white px-4 py-2 rounded">Download SQL Backup</button>
            </form>
            <form method="POST" class="inline-block">
                <button type="submit" name="reset_votes" class="bg-red-500 text-white px-4 py-2 rounded">Reset Voting</button>
            </form>
        </div>

        <!-- Candidate Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($candidates as $candidate): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden relative">
                <img src="<?= htmlspecialchars($candidate['photo']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($candidate['name']) ?></h2>
                    <p class="text-sm text-gray-700 mb-2"><strong>Vision:</strong> <?= htmlspecialchars($candidate['vision']) ?></p>
                    <p class="text-sm text-gray-700 mb-4"><strong>Mission:</strong> <?= htmlspecialchars($candidate['mission']) ?></p>
                    
                    <!-- Vote Count -->
                    <p class="text-sm text-gray-700 mb-4"><strong>Votes:</strong> <span id="vote-count-<?= $candidate['id'] ?>"><?= $candidate['vote_count'] ?></span></p>

                    <!-- Winner Icon -->
                    <div id="winner-icon-<?= $candidate['id'] ?>" class="absolute top-2 right-2 bg-yellow-500 text-white p-2 rounded-full <?= $candidate['id'] === $winnerId ? '' : 'hidden' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div class="flex justify-between">
                        <button onclick="openForm('edit', <?= $candidate['id'] ?>)" class="bg-green-500 text-white px-3 py-1 rounded">Edit</button>
                        <button onclick="deleteCandidate(<?= $candidate['id'] ?>)" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openForm(action, id = null) {
            $('#form-modal').removeClass('hidden');
            if (action === 'add') {
                $('#form-title').text('Add New Candidate');
                $('#candidate-id').val('');
                $('#name').val('');
                $('#photo').val('');
                $('#vision').val('');
                $('#mission').val('');
            } else if (action === 'edit') {
                $('#form-title').text('Edit Candidate');
                $.ajax({
                    url: 'get_candidate.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(data) {
                        const candidate = JSON.parse(data);
                        $('#candidate-id').val(candidate.id);
                        $('#name').val(candidate.name);
                        $('#photo').val(''); // Clear the input for new upload
                        $('#vision').val(candidate.vision);
                        $('#mission').val(candidate.mission);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        }

        function deleteCandidate(id) {
            if (confirm('Are you sure you want to delete this candidate?')) {
                $.ajax({
                    url: 'delete_candidate.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        location.reload(); // Reload the page after successful deletion
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // For debugging if there's an error
                    }
                });
            }
        }

        
        // Function to refresh votes
        function refreshVotes() {
            $.ajax({
                url: 'get_votes.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Update each candidate's vote count and check for the winner
                    data.candidates.forEach(function(candidate) {
                        $('#vote-count-' + candidate.id).text(candidate.vote_count); // Update vote count
                        
                        // If the candidate is the winner, show the winner icon, else hide it
                        if (candidate.id === data.winnerId) {
                            $('#winner-icon-' + candidate.id).removeClass('hidden');
                        } else {
                            $('#winner-icon-' + candidate.id).addClass('hidden');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error refreshing votes:', error);
                }
            });
        }

        // Auto-refresh votes every 5 seconds (5000 ms)
        setInterval(refreshVotes, 5000);
    </script>

    <!-- Modal for Add/Edit Candidate -->
    <div id="form-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded w-full max-w-3xl">
            <h2 id="form-title" class="text-xl mb-4"></h2>
            <form id="candidate-form" method="POST" action="form_handler.php" enctype="multipart/form-data">
                <input type="hidden" id="candidate-id" name="id">
                <div class="mb-4">
                    <label for="name" class="block">Name</label>
                    <input type="text" id="name" name="name" class="border w-full p-2" required>
                </div>
                <div class="mb-4">
                    <label for="photo" class="block">Photo</label>
                    <input type="file" id="photo" name="photo" class="border w-full p-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="vision" class="block">Vision</label>
                    <textarea id="vision" name="vision" class="border w-full p-2" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="mission" class="block">Mission</label>
                    <textarea id="mission" name="mission" class="border w-full p-2" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
                    <button type="button" onclick="$('#form-modal').addClass('hidden');" class="bg-gray-300 text-black px-4 py-2 rounded ml-2">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
