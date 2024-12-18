<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Rental Unit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    
    <h2>Insert Rental Unit</h2>
    <form action="add_rental.php" method="POST">
        Title: <input type="text" name="title" required><br>
        Description: <textarea name="description" required></textarea><br>
        Features (comma-separated): <input type="text" name="features" required><br>
        Price per night: <input type="number" name="price" step="0.01" required><br>
        <button type="submit" name="add_rental">Add Rental</button>
    </form>
</body>
</html>

<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    die("Please login to add a rental unit.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_rental"])) {
    // Gather form inputs
    $username = $_SESSION['username'];
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $features = $_POST['features'] ?? null;
    $price = $_POST['price'] ?? null;
    $post_date = date("Y-m-d");

    // Check daily posting limit
    $stmt = $conn->prepare("SELECT COUNT(*) FROM rental WHERE username = ? AND post_date = ?");
    $stmt->bind_param("ss", $username, $post_date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count >= 2) {
        die("You have reached the limit of 2 rentals per day.");
    }

    // Insert rental unit
    $stmt = $conn->prepare("INSERT INTO rental (username, title, description, features, price, post_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $title, $description, $features, $price, $post_date);   

    if ($stmt->execute()) {
        echo "Rental added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>