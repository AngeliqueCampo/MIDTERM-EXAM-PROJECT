<?php
require_once '../core/dbConfig.php';

// check for appointment ID
if (isset($_GET['appointment_id'])) {
    // get appointment ID
    $appointmentID = $_GET['appointment_id'];
    
    // prepare and execute statement to delete appointment
    $stmt = $pdo->prepare("DELETE FROM Appointments WHERE AppointmentID = :appointment_id");
    $stmt->execute(['appointment_id' => $appointmentID]);

    // redirect to the appointment list view after deletion
    header("Location: view_appointments.php");
    exit();
}
?>
