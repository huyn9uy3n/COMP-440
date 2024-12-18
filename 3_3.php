<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Part 3 - Feature 3</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h2>Filter Rental Units by User</h2>
    <form action="" method="POST">
        <label for="username">Enter Username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Search</button>
    </form>
</body>
</html>

<?php
include 'db.php'; // Include your database connection file
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // Get the username from the form input

    // SQL query to fetch rental units with all reviews as 'Excellent' or 'Good'
    $sql = "
    SELECT DISTINCT r.rental_id, r.username, r.title, r.description, r.features, r.price, r.post_date, v.rating
    FROM rental r
    JOIN review v ON r.rental_id = v.rental_id
    WHERE r.username = ?
      AND NOT EXISTS (
        SELECT 1
        FROM review v2
        WHERE v2.rental_id = r.rental_id
          AND v2.rating NOT IN ('Excellent', 'Good')
      )
    ";

    // Prepare and execute
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display results
    echo "<h2>Rental Units for User: " . htmlspecialchars($username) . "</h2>";
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Rental ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Features</th>
                    <th>Price</th>
                    <th>Post Date</th>
                    <th>Rating</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['rental_id']) . "</td>
                    <td>" . htmlspecialchars($row['title']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['features']) . "</td>
                    <td>" . htmlspecialchars($row['price']) . "</td>
                    <td>" . htmlspecialchars($row['post_date']) . "</td>
                    <td>" . htmlspecialchars($row['rating']) . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No rental units meet the criteria for user: " . htmlspecialchars($username) . "</p>";
    }
    $stmt->close();
    $conn->close();
}
?>