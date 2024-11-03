<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Fetch all veterinarians with added_by, last_updated, and last_updated_by details
$vets = getAllVetsWithDetails($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Veterinarians</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
</head>
<body>
    <h1>Veterinarian List</h1>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
        <div style="color: red; margin: 10px 0;">
            Error: A veterinarian with this name already exists.
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Veterinarian ID</th>
                <th>Veterinarian Name</th>
                <th>Added By</th>
                <th>Last Updated</th>
                <th>Last Updated By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vets)): ?>
                <tr>
                    <td colspan="6">No records found. Please add a veterinarian.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($vets as $vet): ?>
                    <tr>
                        <td><?= htmlspecialchars($vet['VetID']) ?></td>
                        <td><?= htmlspecialchars($vet['VetName']) ?></td>
                        <td><?= htmlspecialchars($vet['added_by']) ?></td>
                        <td><?= htmlspecialchars($vet['last_updated']) ?></td>
                        <td><?= htmlspecialchars($vet['LastUpdatedBy']) ?></td> <!-- Updated here -->
                        <td>
                            <a href="../appointments/view_appointments.php?vet_id=<?= $vet['VetID'] ?>">View Appointments</a> |
                            <a href="../veterinarians/edit_vet.php?vet_id=<?= $vet['VetID'] ?>">Edit</a> |
                            <a href="../veterinarians/delete_vet.php?vet_id=<?= $vet['VetID'] ?>" onclick="return confirm('Are you sure you want to delete this veterinarian?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <br><br><a href="../index.php">Back to Home</a>
</body>
</html>
