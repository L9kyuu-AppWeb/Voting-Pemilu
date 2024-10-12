<?php
session_start(); // Memulai session

// Cek apakah session email ada, jika tidak, redirect ke index.php
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}

// Jika session ada, lanjutkan ke halaman voting
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting System - Vote</title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-purple-100">
    <div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-purple-600 mb-4">Select a Candidate</h2>
        <!-- Tambahkan tombol Logout -->
        <div class="flex justify-between mb-4">
            <p class="text-lg text-gray-700">Welcome, <?php echo $_SESSION['email']; ?></p>
            <form action="logout.php" method="POST">
                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded">Logout</button>
            </form>
        </div>
        <div id="candidate-list" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <!-- Candidates will be generated here -->
        </div>
        <button id="vote-btn" class="mt-4 bg-purple-500 text-white py-2 px-4 rounded w-full">Vote</button>
        <p id="vote-error" class="text-red-500 mt-2"></p>
    </div>

    <script>
        $(document).ready(function() {
            loadCandidates();

            // Ketika tombol vote ditekan
            $('#vote-btn').click(function() {
                var candidate_id = $('input[name="candidate"]:checked').val(); // Ambil kandidat yang dipilih

                if (candidate_id) {
                    $.ajax({
                        url: 'vote.php',
                        method: 'POST',
                        data: {
                            candidate_id: candidate_id
                        },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.success) {
                                alert('Vote successful!');
                                // Redirect ke halaman konfirmasi atau bersihkan pilihan
                            } else {
                                $('#vote-error').text(result.message);
                            }
                        }
                    });
                } else {
                    $('#vote-error').text('Please select a candidate.');
                }
            });
        });

        function loadCandidates() {
            $.ajax({
                url: 'get_candidates.php',
                method: 'GET',
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success && Array.isArray(result.candidates)) {
                        var candidates = result.candidates;
                        var html = '';
                        candidates.forEach(function(candidate) {
                            html += `
                                <div class="candidate-card bg-white shadow-md rounded-lg overflow-hidden transition-colors duration-300 cursor-pointer" data-id="${candidate.id}">
                                    <div class="relative">
                                        <img src="candidates/${candidate.photo}" alt="${candidate.name}" class="w-full h-auto">
                                        <div class="absolute top-2 left-2">
                                            <input type="radio" name="candidate" value="${candidate.id}" class="form-radio h-4 w-4 text-purple-600 hidden">
                                        </div>
                                    </div>
                                    <div class="p-4 text-left">
                                        <h3 class="text-lg font-semibold mb-2">${candidate.name}</h3>
                                        <p class="text-gray-700"><strong>Vision:<br></strong> ${candidate.vision}</p>
                                        <p class="text-gray-700"><strong>Mission:<br></strong> ${candidate.mission}</p>
                                    </div>
                                </div>
                            `;
                        });
                        $('#candidate-list').html(html);

                        // Tambahkan event listener untuk animasi pemilihan
                        $('.candidate-card').click(function() {
                            // Hapus highlight dari semua card
                            $('.candidate-card').removeClass('bg-yellow-200');

                            // Tambahkan highlight pada card yang dipilih
                            $(this).addClass('bg-yellow-200');

                            // Pilih radio button di dalam card ini
                            $(this).find('input[type="radio"]').prop('checked', true);
                        });
                    } else {
                        $('#candidate-list').html('<p>No candidates available.</p>');
                    }
                },
                error: function() {
                    $('#candidate-list').html('<p>Failed to load candidates.</p>');
                }
            });
        }
    </script>
</body>
</html>
