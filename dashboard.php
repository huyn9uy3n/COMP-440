<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</h1>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='logout.php'>Logout</a>
    </nav>

    
    <a href='add_rental.php'>Add Rental</a>
    <a href='search_rental.php'>Search Rental</a>
    <br>
    <a href='3_1.php'>1. List the most expensive rental units for each feature</a>
    <br>
    <a href='3_2.php'>2. List the users who posted at least two rental units that were posted on the same day</a>
    <br>
    <a href='3_3.php'>3. List all the rental units posted by user X, such that all the comments are "Excellent" or "Good"
    for these rental units</a>
    <br>
    <a href='3_4.php'>4. List the users who posted the most number of rental units on a day</a>
    <br>
    <a href='3_5.php'>5. Display all the users who posted some reviews, but each of them is "poor"</a>
    <br>
    <a href='3_6.php'>6. Display those users such that each rental unit they posted so far never received any "poor"
    reviews</a>
</body>
</html>