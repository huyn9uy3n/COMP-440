<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Part 3 - Feature 4</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h2>Find Users Who Posted the Most Rental Units</h2>
    <form action="" method="POST">
        <label for="post_date">Enter Post Date (YYYY-MM-DD):</label>
        <input type="date" id="post_date" name="post_date" required>
        <button type="submit">Search</button>
    </form>
</body>
</html>

<?php
include 'db.php'; // Include your database connection file
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['post_date']; // Get the date from the form input

    // SQL query to find the users who posted the most rental units on the specific day
    $sql = "
    SELECT username, COUNT(*) AS rental_count
    FROM rental
    WHERE post_date = ?
    GROUP BY username
    HAVING rental_count = (
        SELECT MAX(rental_count)
        FROM (
            SELECT username, COUNT(*) AS rental_count
            FROM rental
            WHERE post_date = ?
            GROUP BY username
        ) AS max_rentals
    )
    ";

    // Prepare and execute
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $date, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display results
    echo "<h2>Users Who Posted the Most Rental Units on " . htmlspecialchars($date) . "</h2>";
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Username</th>
                    <th>Rental Units Posted</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['rental_count']) . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No rental units posted on " . htmlspecialchars($date) . "</p>";
    }
    $stmt->close();
    $conn->close();
}
?>