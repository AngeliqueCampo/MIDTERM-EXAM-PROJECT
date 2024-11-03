<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

$updatedBy = $_SESSION['user_id'] ?? null;
$vets = getAllVets($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'appointmentID' => $_POST['appointment_id'],
        'vetID' => $_POST['vet_id'],
        'petName' => $_POST['pet_name'],
        'ownerName' => $_POST['owner_name'],
        'appointmentDate' => $_POST['appointment_date'],
        'appointmentTime' => $_POST['appointment_time']
    ];

    if (editAppointment($pdo, $data, $updatedBy)) {
        header("Location: view_appointments.php?vet_id=" . $data['vetID']);
        exit();
    } else {
        echo "Error updating appointment.";
    }
}

if (isset($_GET['appointment_id'])) {
    $appointmentID = $_GET['appointment_id'];
    $stmt = $pdo->prepare("SELECT * FROM Appointments WHERE AppointmentID = :appointment_id");
    $stmt->execute(['appointment_id' => $appointmentID]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Edit Appointment</h1>
    <form action="edit_appointment.php" method="POST">
        <!-- hidden field to store appointment ID -->
        <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['AppointmentID']) ?>">

        <!-- dropdown to select vet for appointment -->
        <label for="vet_id">Veterinarian:</label>
        <select name="vet_id">
            <?php foreach ($vets as $vet): ?>
                <option value="<?= $vet['VetID'] ?>" <?= $vet['VetID'] == $appointment['VetID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($vet['VetName']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- input fields for appointment details -->
        <label for="pet_name">Pet Name:</label>
        <input type="text" name="pet_name" value="<?= htmlspecialchars($appointment['PetName']) ?>"><br><br>

        <label for="owner_name">Owner Name:</label>
        <input type="text" name="owner_name" value="<?= htmlspecialchars($appointment['OwnerName']) ?>"><br><br>

        <label for="appointment_date">Appointment Date:</label>
        <input type="date" name="appointment_date" value="<?= htmlspecialchars($appointment['AppointmentDate']) ?>"><br><br>

        <label for="appointment_time">Appointment Time:</label>
        <input type="time" name="appointment_time" value="<?= htmlspecialchars($appointment['AppointmentTime']) ?>"><br><br>

        <button type="submit">Update Appointment</button>
    </form>

    <!-- link to go back to list -->
    <br><a href="view_appointments.php?vet_id=<?= htmlspecialchars($appointment['VetID']) ?>">Back to Appointments</a>
</body>
</html>
