<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Part 3 - Feature 2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h1>Search Users by Rental Features</h1>

    <form method="POST" action="">
        <label for="feature_x">Feature X:</label>
        <input type="text" id="feature_x" name="feature_x" required>
        <label for="feature_y">Feature Y:</label>
        <input type="text" id="feature_y" name="feature_y" required>
        <button type="submit">Search</button>
    </form>
</body>
</html>

<?php
include 'db.php'; // Database connection
session_start(); // Start the session if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feature_x = trim($_POST['feature_x']);
    $feature_y = trim($_POST['feature_y']);

    if (empty($feature_x) || empty($feature_y)) {
        $error = "Both features are required.";
    } else {
        // Query to find users who posted at least two rental units on the same day with features X and Y
        $query = "
            WITH parsed_features AS (
                SELECT
                    rental_id,
                    username,
                    post_date,
                    TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(features, ',', n.n), ',', -1)) AS feature
                FROM
                    rental
                JOIN
                    (SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5) n
                    ON LENGTH(features) - LENGTH(REPLACE(features, ',', '')) + 1 >= n.n
            )
            SELECT
                r1.username,
                r1.post_date,
                r1.rental_id AS rental_x,
                r2.rental_id AS rental_y
            FROM
                parsed_features r1
            JOIN
                parsed_features r2
                ON r1.username = r2.username
                AND r1.post_date = r2.post_date
                AND r1.feature = ?
                AND r2.feature = ?
                AND r1.rental_id != r2.rental_id
            GROUP BY
                r1.username, r1.post_date, r1.rental_id, r2.rental_id
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $feature_x, $feature_y);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (isset($result) && $result->num_rows > 0) {
            echo "<table border='1'>
            <tr>
                <th>Username</th>
                <th>Post Date</th>
                <th>Rental ID with Feature X</th>
                <th>Rental ID with Feature Y</th>
            </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['post_date']) . "</td>
                    <td>" . htmlspecialchars($row['rental_x']) . "</td>
                    <td>" . htmlspecialchars($row['rental_y']) . "</td>
                </tr>";
            }

            echo "</table>";
        } elseif (isset($result) && $result->num_rows == 0) {
            echo "<p>No results found for the given features</p>";
        }

        if (!$result) {
            $error = "Error executing query: " . $conn->error;
        }
    }
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
