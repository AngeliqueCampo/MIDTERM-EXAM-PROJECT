<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (isset($_GET['vet_id'])) {
    $vet_id = intval($_GET['vet_id']);
    
    $stmt = $pdo->prepare("
        SELECT Appointments.*, Veterinarians.VetName, AddedByUser.Username AS added_by, LastUpdatedByUser.Username AS last_updated_by
        FROM Appointments
        JOIN Veterinarians ON Appointments.VetID = Veterinarians.VetID
        LEFT JOIN Users AS AddedByUser ON Appointments.added_by = AddedByUser.UserID
        LEFT JOIN Users AS LastUpdatedByUser ON Appointments.LastUpdatedBy = LastUpdatedByUser.UserID
        WHERE Appointments.VetID = ?
    ");
    $stmt->execute([$vet_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $vet_stmt = $pdo->prepare("SELECT VetName FROM Veterinarians WHERE VetID = ?");
    $vet_stmt->execute([$vet_id]);
    $vet = $vet_stmt->fetch(PDO::FETCH_ASSOC);
    $vetName = $vet ? htmlspecialchars($vet['VetName']) : 'Unknown Veterinarian';
} else {
    echo "<p>Error: Veterinarian ID is not specified.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments for Dr. <?= $vetName ?></title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
</head>
<body>
    <h1>Appointments for Dr. <?= $vetName ?></h1>
    
    <form action="../core/handleForms.php" method="POST">
        <input type="hidden" name="type" value="appointment">
        <input type="hidden" name="vetID" value="<?= $vet_id ?>">
        
        <label for="petName">Pet Name:</label>
        <input type="text" name="petName" required><br><br>
        
        <label for="ownerName">Owner Name:</label>
        <input type="text" name="ownerName" required><br><br>
        
        <label for="appointmentDate">Appointment Date:</label>
        <input type="date" name="appointmentDate" required><br><br>
        
        <label for="appointmentTime">Appointment Time:</label>
        <input type="time" name="appointmentTime" required><br><br>
        
        <button type="submit" name="insertAppointmentBtn">Add Appointment</button>
    </form>
    
    <br><br>
    
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Pet Name</th>
                <th>Owner Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Added By</th>
                <th>Last Updated</th>
                <th>Last Updated By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="9">No appointments found for this veterinarian.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['AppointmentID']) ?></td>
                        <td><?= htmlspecialchars($appointment['PetName']) ?></td>
                        <td><?= htmlspecialchars($appointment['OwnerName']) ?></td>
                        <td><?= htmlspecialchars($appointment['AppointmentDate']) ?></td>
                        <td><?= htmlspecialchars($appointment['AppointmentTime']) ?></td>
                        <td><?= htmlspecialchars($appointment['added_by']) ?></td>
                        <td><?= htmlspecialchars($appointment['last_updated']) ?></td>
                        <td><?= htmlspecialchars($appointment['last_updated_by']) ?></td>
                        <td>
                            <a href="edit_appointment.php?appointment_id=<?= $appointment['AppointmentID'] ?>&vet_id=<?= $vet_id ?>">Edit</a> |
                            <a href="../core/handleForms.php?action=delete&type=appointment&appointment_id=<?= $appointment['AppointmentID'] ?>&vet_id=<?= $vet_id ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <br><br><a href="../index.php">Back to Home</a>
</body>
</html>
