<?php
require_once '../core/dbConfig.php';

if (isset($_GET['vet_id'])) {
    $vetID = $_GET['vet_id'];
    
    // delete all associated appointments
    $stmt = $pdo->prepare("DELETE FROM Appointments WHERE VetID = :vet_id");
    $stmt->execute(['vet_id' => $vetID]);

    // delete veterinarian
    $stmt = $pdo->prepare("DELETE FROM Veterinarians WHERE VetID = :vet_id");
    $stmt->execute(['vet_id' => $vetID]);

    header("Location: view_vets.php");
    exit();
}
?>
