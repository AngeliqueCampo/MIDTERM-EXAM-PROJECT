<?php
require_once 'models.php';
require_once 'dbConfig.php';

// function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

// process form submissions if == POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // handle user registration form
    if (isset($_POST['type']) && $_POST['type'] === 'register' && isset($_POST['registerBtn'])) {
        // Sanitize and assign form input data
        $firstName = sanitizeInput($_POST['firstName']);
        $lastName = sanitizeInput($_POST['lastName']);
        $address = sanitizeInput($_POST['address']);
        $username = sanitizeInput($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

        // attempt to register the user
        if (registerUser($pdo, $firstName, $lastName, $address, $username, $password)) {
            header("Location: ../auth/login.php");
            exit();
        } else {
            header("Location: ../auth/register.php?error=registration_failed");
            exit();
        }
    }

    // handle adding a veterinarian
    elseif (isset($_POST['type']) && $_POST['type'] === 'vet' && isset($_POST['insertVetBtn'])) {
        $vetName = sanitizeInput($_POST['vetName']);
        $userID = $_SESSION['user_id'] ?? null; 

        // add veterinarian
        if ($userID && addVeterinarian($pdo, $vetName, $userID)) {
            header("Location: ../veterinarians/view_vets.php");
            exit();
        } else {
            header("Location: ../veterinarians/view_vets.php?error=duplicate");
            exit();
        }
    }

    // handle adding an appointment
    elseif (isset($_POST['type']) && $_POST['type'] === 'appointment' && isset($_POST['insertAppointmentBtn'])) {
        if (empty($_POST['vetID'])) {
            echo "Error: Veterinarian ID is not specified.";
            exit();
        }

        // sanitize appointment data
        $appointmentData = [
            'vetID' => sanitizeInput($_POST['vetID']),
            'petName' => sanitizeInput($_POST['petName']),
            'ownerName' => sanitizeInput($_POST['ownerName']),
            'appointmentDate' => sanitizeInput($_POST['appointmentDate']),
            'appointmentTime' => sanitizeInput($_POST['appointmentTime']),
            'added_by' => $_SESSION['user_id'] ?? null,
        ];

        // attempt to add appointment
        try {
            if (addAppointment($pdo, $appointmentData)) {
                header("Location: ../appointments/view_appointments.php?vet_id=" . $_POST['vetID']);
                exit();
            } else {
                echo "Failed to add appointment. Please check input values.";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

// handle GET requests for deletion actions
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    
    // handle deletion of a veterinarian
    if (isset($_GET['type']) && $_GET['type'] === 'vet' && isset($_GET['vet_id'])) {
        $vetID = sanitizeInput($_GET['vet_id']); // sanitize the veterinarian ID

        // attempt to delete veterinarian
        if (deleteVeterinarian($pdo, $vetID)) {
            header("Location: ../veterinarians/view_vets.php");
            exit();
        } else {
            echo "Error: Failed to delete veterinarian.";
        }
    }

    // handle deletion of an appointment
    elseif (isset($_GET['type']) && $_GET['type'] === 'appointment' && isset($_GET['appointment_id'])) {
        $appointmentID = sanitizeInput($_GET['appointment_id']);

        // attempt to delete appointment and redirect
        try {
            if (deleteAppointment($pdo, $appointmentID)) {
                header("Location: ../appointments/view_appointments.php?vet_id=" . $_GET['vet_id']);
                exit();
            } else {
                echo "Error: Failed to delete appointment.";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Missing or invalid ID for deletion.";
    }
}
?>
