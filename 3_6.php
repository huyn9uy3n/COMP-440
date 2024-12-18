<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Part 3 - Feature 6</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h2>Users with Rental Units That Never Received 'Poor' Reviews</h2>
</body>
</html>

<?php
include 'db.php'; // Include your database connection file
session_start();

// SQL query to get users and their rental units that never received "Poor" reviews
$sql = "
    SELECT r.rental_id, r.title, r.username, rv.rating
    FROM rental r
    LEFT JOIN review rv ON r.rental_id = rv.rental_id
    WHERE rv.rating != 'Poor' OR rv.rating IS NULL
    GROUP BY r.rental_id, r.username, r.title
    HAVING COUNT(rv.review_id) > 0 OR COUNT(rv.review_id) = 0
";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Display the results
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Username</th>
                <th>Rental ID</th>
                <th>Title</th>
                <th>Rating</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['rental_id']) . "</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['rating'] ?? 'No Reviews') . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found with rental units that have never received 'Poor' reviews.</p>";
}

$stmt->close();
$conn->close();
?>
