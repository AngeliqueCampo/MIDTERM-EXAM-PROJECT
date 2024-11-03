<?php
session_start();
require_once '../core/dbConfig.php';

// function to sanitize and trim input data
function validate_input($data) {
    return htmlspecialchars(trim($data));
}

// array to store any validation error messages
$errors = [];

// process the form if == POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitize form inputs
    $firstName = validate_input($_POST['firstName']);
    $lastName = validate_input($_POST['lastName']);
    $username = validate_input($_POST['username']);
    $password = $_POST['password'];

    // validate each input and store error if any fields are empty
    if (empty($firstName)) $errors['firstName'] = "First name is required.";
    if (empty($lastName)) $errors['lastName'] = "Last name is required.";
    if (empty($username)) $errors['username'] = "Username is required.";
    if (empty($password)) $errors['password'] = "Password is required.";

    // check for duplicate username if no errors so far
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        $usernameExists = $stmt->fetchColumn();

        if ($usernameExists) {
            $errors['username'] = "Username already exists. Please choose a different one.";
        }
    }

    // proceed with registration if there are no validation errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // prepare statement to insert user data
        $stmt = $pdo->prepare("INSERT INTO Users (FirstName, LastName, Username, Password) VALUES (?, ?, ?, ?)");

        // execute the prepared statement
        if ($stmt->execute([$firstName, $lastName, $username, $hashedPassword])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: login.php");
            exit();
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Veterinary Clinic Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Register</h2>
            <form method="POST">
                <!-- First Name, Last Name, username, password input validation -->
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" value="<?= htmlspecialchars($firstName ?? '') ?>" required>
                <?= $errors['firstName'] ?? '' ?>

                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" value="<?= htmlspecialchars($lastName ?? '') ?>" required>
                <?= $errors['lastName'] ?? '' ?>

                <label for="username">Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                <?= $errors['username'] ?? '' ?>

                <label for="password">Password:</label>
                <input type="password" name="password" required>
                <?= $errors['password'] ?? '' ?>

                <input type="submit" value="Register">
            </form>

            <!-- error message if registration fails -->
            <?php if (!empty($errors['general'])): ?>
                <div class="error-message"><?= $errors['general'] ?></div>
            <?php endif; ?>

            <!-- login page for users with existing accounts -->
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
