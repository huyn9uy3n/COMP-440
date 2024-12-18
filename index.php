<?php
session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    // Redirect to dashboard.php
    header("Location: dashboard.php");
    exit(); // Ensure to exit after redirection
}

// If not logged in, continue to show the welcome page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Rental System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Rental System</h1>
    <nav>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </nav>
</body>
</html>
