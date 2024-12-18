<?php
include 'db.php'; // Database connection
session_start(); // Start the session

if (!isset($_GET['rental_id'])) {
    die("Rental ID is required.");
}

$rental_id = $_GET['rental_id']; // Retrieve rental ID from URL
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Review</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h2>Add Review for Rental Unit ID: <?php echo htmlspecialchars($rental_id); ?></h2>
    <form action="add_review.php?rental_id=<?php echo $rental_id; ?>" method="POST">
        <label for="rating">Rating:</label>
        <select name="rating" id="rating" required>
            <option value="Excellent">Excellent</option>
            <option value="Good">Good</option>
            <option value="Fair">Fair</option>
            <option value="Poor">Poor</option>
        </select><br>

        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment"></textarea><br>

        <button type="submit">Submit Review</button>
    </form>
</body>
</html>

<?php


if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Get the logged-in user's username
} else {
    die("You must be logged in to add a review.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $review_date = date("Y-m-d");

    // Check if the user has already reviewed this rental unit
    $stmt = $conn->prepare("SELECT COUNT(*) FROM review WHERE rental_id = ? AND username = ?");
    $stmt->bind_param("is", $rental_id, $username);
    $stmt->execute();
    $stmt->bind_result($review_count);
    $stmt->fetch();
    $stmt->close();

    if ($review_count > 0) {
        die("You have already reviewed this rental unit.");
    }

    // Check if the rental unit belongs to the user
    $stmt = $conn->prepare("SELECT username FROM rental WHERE rental_id = ?");
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
    $stmt->bind_result($owner_username);
    $stmt->fetch();
    $stmt->close();

    if ($owner_username === $username) {
        die("You cannot review your own rental unit.");
    }

    // Check the number of reviews the user has submitted today
    $stmt = $conn->prepare("SELECT COUNT(*) FROM review WHERE username = ? AND review_date = ?");
    $stmt->bind_param("ss", $username, $review_date);
    $stmt->execute();
    $stmt->bind_result($daily_review_count);
    $stmt->fetch();
    $stmt->close();

    if ($daily_review_count >= 3) {
        die("You can only submit a maximum of 3 reviews per day.");
    }

    // Insert the review into the database
    $stmt = $conn->prepare("INSERT INTO review (rental_id, username, rating, comment, review_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $rental_id, $username, $rating, $comment, $review_date);

    if ($stmt->execute()) {
        echo "Review added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} 
?>
