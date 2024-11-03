<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Assuming session holds the logged-in user ID
$updatedBy = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vetID = $_POST['vet_id'];
    $vetName = $_POST['vet_name'];
    
    editVeterinarian($pdo, $vetID, $vetName, $updatedBy);

    header("Location: view_vets.php");
    exit();
}

if (isset($_GET['vet_id'])) {
    $vetID = $_GET['vet_id'];
    $stmt = $pdo->prepare("SELECT * FROM Veterinarians WHERE VetID = :vet_id");
    $stmt->execute(['vet_id' => $vetID]);
    $vet = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Veterinarian</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
</head>
<body>
    <h1>Edit Veterinarian</h1>
    <?php if (isset($vet)): ?>
        <!-- form to update vet details -->
        <form action="edit_vet.php" method="post">
            <input type="hidden" name="vet_id" value="<?= htmlspecialchars($vet['VetID']) ?>">
            <label for="vet_name">Veterinarian Name:</label>
            <input type="text" name="vet_name" value="<?= htmlspecialchars($vet['VetName']) ?>" required>
            <button type="submit">Update Veterinarian</button>
        </form>
    <?php endif; ?>

    <!-- link to go back -->
    <a href="view_vets.php">Back to Veterinarian List</a>
</body>
</html>
