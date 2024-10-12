<?php
include '../koneksi.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold text-center mb-8">Perhitungan Real Time</h1>        

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($candidates as $candidate): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden relative">
                <img src="<?= htmlspecialchars($candidate['photo']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($candidate['name']) ?></h2>
                    <p class="text-sm text-gray-700 mb-2"><strong>Vision:</strong> <?= htmlspecialchars($candidate['vision']) ?></p>
                    <p class="text-sm text-gray-700 mb-4"><strong>Mission:</strong> <?= htmlspecialchars($candidate['mission']) ?></p>
                    
                    <!-- Vote count -->
                    <p class="text-sm text-gray-700 mb-4"><strong>Votes:</strong> <span id="vote-count-<?= $candidate['id'] ?>"><?= $candidate['vote_count'] ?></span></p>

                    <!-- Winner icon -->
                    <div id="winner-icon-<?= $candidate['id'] ?>" class="absolute top-2 right-2 bg-yellow-500 text-white p-2 rounded-full <?= $candidate['id'] === $winnerId ? '' : 'hidden' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>                    
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
                
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
    
</body>
</html>
