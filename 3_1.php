<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Part 3 - Feature 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>
    <h1>Most Expensive Rentals by Feature</h1>
</body>
</html>

<?php
include 'db.php'; // Database connection
session_start(); // Start the session if needed

// Query to find the most expensive rental unit for each feature
$query = "
    WITH parsed_features AS (
        SELECT
            rental_id,
            title,
            price,
            post_date,
            username,
            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(features, ',', n.n), ',', -1)) AS feature
        FROM
            rental
        JOIN
            (SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5) n
            ON LENGTH(features) - LENGTH(REPLACE(features, ',', '')) + 1 >= n.n
    )
    SELECT
        feature,
        rental_id,
        title AS rental_title,
        price
    FROM
        parsed_features pf1
    WHERE
        price = (
            SELECT MAX(price)
            FROM parsed_features pf2
            WHERE pf1.feature = pf2.feature
        )
    GROUP BY feature, rental_id, rental_title, price
    ORDER BY feature, price DESC;
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>
                <tr>
                    <th>Feature</th>
                    <th>Rental ID</th>
                    <th>Rental Title</th>
                    <th>Price</th>
                </tr>";
    $displayed_features = []; // To track features that have already been displayed

    while ($row = $result->fetch_assoc()) {
        // If this feature has already been displayed, skip it
        if (in_array($row['feature'], $displayed_features)) {
            continue;
        }

        // Display the feature and rental info
        echo "<tr>
            <td>" . htmlspecialchars($row['feature']) . "</td>
            <td>" . htmlspecialchars($row['rental_id']) . "</td>
            <td>" . htmlspecialchars($row['rental_title']) . "</td>
            <td>" . htmlspecialchars($row['price'], 2) . "</td>
        </tr>";

        // Mark this feature as displayed
        $displayed_features[] = $row['feature'];
    }
    echo "</table>";
} else {
    echo "<p>No data found</p>";
}

if (!$result) {
    die("Error executing query: " . $conn->error);
}

$conn->close();
?>
