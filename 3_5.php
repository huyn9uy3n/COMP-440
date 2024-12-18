<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Part 3 - Feature 5</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h2>Find Users Who Posted the Poor Review</h2>
</body>
</html>

<?php
include 'db.php'; // Include your database connection file
session_start();

// SQL query to find users who posted reviews and only have "Poor" reviews
$sql = "
    SELECT DISTINCT r.username
    FROM review r
    WHERE NOT EXISTS (
        SELECT 1
        FROM review r2
        WHERE r2.username = r.username
        AND r2.rating != 'Poor'
    )
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Display the results
echo "<h2>Users Who Posted Only 'Poor' Reviews</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Username</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['username']) . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found who posted only 'Poor' reviews.</p>";
}

$stmt->close();
$conn->close();
?>
