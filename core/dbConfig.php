<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "veterinary_clinic";

// start session if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // establish PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
