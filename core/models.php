<?php
require_once 'dbConfig.php';
require_once 'handleForms.php';

// veterinarian operations

// add a new veterinarian
function addVeterinarian($pdo, $vetName, $addedBy) {
    $vetName = sanitizeInput($vetName);
    // check for duplicate veterinarian
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Veterinarians WHERE VetName = :vetName");
    $stmt->execute(['vetName' => $vetName]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        return false; 
    }

    // insert new veterinarian record
    $stmt = $pdo->prepare("INSERT INTO Veterinarians (VetName, added_by) VALUES (:vetName, :addedBy)");
    return $stmt->execute(['vetName' => $vetName, 'addedBy' => $addedBy]);
}

// edit veterinarian details
function editVeterinarian($pdo, $vetID, $vetName, $updatedBy) {
    $vetName = sanitizeInput($vetName);
    $stmt = $pdo->prepare("UPDATE Veterinarians 
                           SET VetName = :vetName, LastUpdatedBy = :updatedBy 
                           WHERE VetID = :vetID");
    return $stmt->execute(['vetName' => $vetName, 'vetID' => $vetID, 'updatedBy' => $updatedBy]);
}

// delete a veterinarian and associated appointments
function deleteVeterinarian($pdo, $vetID) {
    try {
        // Delete related appointments first
        $stmt = $pdo->prepare("DELETE FROM Appointments WHERE VetID = :vetID");
        $stmt->execute(['vetID' => $vetID]);

        // Delete the veterinarian record
        $stmt = $pdo->prepare("DELETE FROM Veterinarians WHERE VetID = :vetID");
        return $stmt->execute(['vetID' => $vetID]);
    } catch (PDOException $e) {
        error_log("Delete Veterinarian Error: " . $e->getMessage());
        return false;
    }
}

// appointment operations

// add a new appointment 
function addAppointment($pdo, $data) {
    foreach ($data as $key => $value) {
        $data[$key] = sanitizeInput($value);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO Appointments (VetID, PetName, OwnerName, AppointmentDate, AppointmentTime, added_by) 
                               VALUES (:vetID, :petName, :ownerName, :appointmentDate, :appointmentTime, :addedBy)");
        return $stmt->execute([
            'vetID' => $data['vetID'],
            'petName' => $data['petName'],
            'ownerName' => $data['ownerName'],
            'appointmentDate' => $data['appointmentDate'],
            'appointmentTime' => $data['appointmentTime'],
            'addedBy' => $data['added_by']
        ]);
    } catch (Exception $e) {
        error_log("Add Appointment Error: " . $e->getMessage());
        return false;
    }
}

// edit an existing appointment
function editAppointment($pdo, $data, $updatedBy) {
    foreach ($data as $key => $value) {
        $data[$key] = sanitizeInput($value);
    }

    try {
        $stmt = $pdo->prepare("UPDATE Appointments 
                               SET VetID = :vetID, PetName = :petName, OwnerName = :ownerName, 
                                   AppointmentDate = :appointmentDate, AppointmentTime = :appointmentTime, 
                                   LastUpdatedBy = :updatedBy
                               WHERE AppointmentID = :appointmentID");
        return $stmt->execute([
            'vetID' => $data['vetID'],
            'petName' => $data['petName'],
            'ownerName' => $data['ownerName'],
            'appointmentDate' => $data['appointmentDate'],
            'appointmentTime' => $data['appointmentTime'],
            'appointmentID' => $data['appointmentID'],
            'updatedBy' => $updatedBy
        ]);
    } catch (Exception $e) {
        error_log("Edit Appointment Error: " . $e->getMessage());
        return false;
    }
}

// delete an appointment by ID
function deleteAppointment($pdo, $appointmentID) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Appointments WHERE AppointmentID = :appointmentID");
        return $stmt->execute(['appointmentID' => $appointmentID]);
    } catch (PDOException $e) {
        error_log("Delete Appointment Error: " . $e->getMessage());
        return false;
    }
}

// retrieve all veterinarians with added_by and last_updated_by user details
function getAllVetsWithDetails($pdo) {
    $stmt = $pdo->prepare("
        SELECT Veterinarians.VetID, Veterinarians.VetName, 
               AddedByUser.Username AS added_by, 
               LastUpdatedByUser.Username AS LastUpdatedBy, 
               Veterinarians.last_updated
        FROM Veterinarians
        LEFT JOIN Users AS AddedByUser ON Veterinarians.added_by = AddedByUser.UserID
        LEFT JOIN Users AS LastUpdatedByUser ON Veterinarians.LastUpdatedBy = LastUpdatedByUser.UserID
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// retrieve all veterinarians
function getAllVets($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM Veterinarians");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
