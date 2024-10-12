<?php
include '../koneksi.php';

// Fetch candidates with their vote counts
$stmt = $pdo->query('
    SELECT c.id, c.name, 
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

// Return the data in JSON format
echo json_encode([
    'candidates' => $candidates,
    'winnerId' => $winnerId
]);
