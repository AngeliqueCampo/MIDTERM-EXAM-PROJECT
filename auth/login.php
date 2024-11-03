<?php
session_start();
require_once '../core/dbConfig.php';

// function to sanitize and trim input data
function validate_input($data) {
    return htmlspecialchars(trim($data));
}

// array to store any validation error messages
$error_message = '';

// process the form if == POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // sanitize form inputs
    $username = validate_input($_POST['username']);
    $password = $_POST['password'];

    // check if fields are filled
    if (!empty($username) && !empty($password)) {

        // prepare statement to retrieve username
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // verify password if user is found
        if ($user && password_verify($password, $user['Password'])) {

            // set session variable and redirect to homepage
            $_SESSION['user_id'] = $user['UserID'];
            header("Location: ../index.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Veterinary Clinic Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Login</h2>
            <form method="POST">
                <!-- username and password input field -->
                <label for="username">Username:</label>
                <input type="text" name="username" required>
                
                <label for="password">Password:</label>
                <input type="password" name="password" required>
                
                <input type="submit" value="Login">
            </form>

            <!-- error message if authentication fails -->
            <?php if ($error_message): ?>
                <div class="error-message"><?= $error_message ?></div>
            <?php endif; ?>

            <!-- registration page for new users -->
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
