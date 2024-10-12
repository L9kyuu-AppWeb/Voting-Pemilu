<?php
// Database connection details
$dbhost = 'localhost';
$dbname = 'db_voting-panitia';
$dbuser = 'root';
$dbpass = ''; // Leave empty if no password

try {
    // Establish a PDO connection using the variables
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    
    // Set error mode to Exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
