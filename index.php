<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// check if the user is logged in
// if not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// fetch username for logged-in user
$stmt = $pdo->prepare("SELECT Username FROM Users WHERE UserID = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user ? htmlspecialchars($user['Username']) : 'User'; // defaults to 'User' if no username found

// check if vet ID is in URL 
$selectedVetID = isset($_GET['vet_id']) ? intval($_GET['vet_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinary Clinic Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            background-image: url('Wall.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px; 
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 700px;
            background-color: rgba(255, 255, 255, 0.85); 
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            border: 2px solid #333;
            text-align: center;
        }
        h1 {
            font-size: 28px;
            font-family: 'Playfair Display', serif;
            color: #333;
            margin-bottom: 10px;
        }
        h3 {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        section h2 {
            font-size: 22px;
            color: #333;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
            margin: 20px 0 10px;
            text-align: left;
        }
        form {
            text-align: left;
            margin-top: 10px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="date"], input[type="time"], select {
            width: calc(100% - 22px);
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center; 
            justify-content: center; 
            height: 40px; 
            line-height: normal;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .vet-container {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(240, 240, 240, 0.9);
        }
        .vet-container h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .vet-actions a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }
        .vet-actions a:hover {
            text-decoration: underline;
        }
        .logout-top-right {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .logout-top-right a {
            color: #d9534f;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .logout-top-right a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
<!-- logout link -->
<div class="logout-top-right">
    <a href="auth/logout.php">Logout</a>
</div>

<!-- main content -->
<div class="container">
    <h1>Welcome to the Veterinary Clinic Management System</h1>
    <h3>Hi, <?= $username; ?>.</h3>

    <!-- section to add veterinarian -->
    <section>
        <h2>Add New Veterinarian</h2>
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="type" value="vet">
            <label for="vetName">Veterinarian Name:</label> 
            <input type="text" name="vetName" required>
            <input type="submit" name="insertVetBtn" value="Add Veterinarian">
        </form>
    </section>

    <!-- section to add appointment -->
    <section>
        <h2>Add New Appointment</h2>
        <form action="core/handleForms.php" method="POST">
            <!-- Add the missing type field for appointment -->
            <input type="hidden" name="type" value="appointment">
            
            <label for="petName">Pet Name:</label>
            <input type="text" name="petName" required>

            <label for="ownerName">Owner Name:</label>
            <input type="text" name="ownerName" required>

            <label for="appointmentDate">Appointment Date:</label>
            <input type="date" name="appointmentDate" required>

            <label for="appointmentTime">Appointment Time:</label>
            <input type="time" name="appointmentTime" required>
            
            <!-- dropdown to select a vet from the database-->
            <label for="vetID">Select Veterinarian:</label>
            <select name="vetID" required>
                <?php 
                $vets = getAllVets($pdo); 
                foreach ($vets as $vet) { 
                    $isSelected = ($selectedVetID === intval($vet['VetID'])) ? 'selected' : ''; // pre-select if vet_id matches
                    ?>
                    <option value="<?= htmlspecialchars($vet['VetID']); ?>" <?= $isSelected; ?>>
                        <?= htmlspecialchars($vet['VetName']); ?>
                    </option>
                <?php } ?>
            </select>
            
            <input type="submit" name="insertAppointmentBtn" value="Add Appointment">
        </form>
    </section>

    <!-- section listing all veterinarians -->
    <section>
        <h2>Current Veterinarians</h2>
        <?php 
        $allVets = getAllVets($pdo); 
        foreach ($allVets as $vet) { ?>
            <div class="vet-container">
                <h3>Dr. <?= htmlspecialchars($vet['VetName']); ?></h3>
                <div class="vet-actions">
                    <a href="appointments/view_appointments.php?vet_id=<?= $vet['VetID']; ?>">View Appointments</a>
                    <a href="index.php?vet_id=<?= $vet['VetID']; ?>">Add Appointment</a>
                    <a href="veterinarians/edit_vet.php?vet_id=<?= $vet['VetID']; ?>">Edit</a>
                    <a href="veterinarians/delete_vet.php?vet_id=<?= $vet['VetID']; ?>" onclick="return confirm('Are you sure you want to delete this veterinarian?');">Delete</a>
                </div>
            </div>
        <?php } ?>
    </section>


</div>

</body>
</html>
