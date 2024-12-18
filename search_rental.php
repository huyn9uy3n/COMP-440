<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Rentals by Feature</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href='dashboard.php'>Dashboard</a>
    </nav>

    <h2>Search Rentals</h2>
    <form action="search_rental.php" method="POST">
        Feature: <input type="text" name="features" required><br>
        <button type="submit">Search</button>
    </form>
</body>
</html>

<?php
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $features = $_POST['features'];

    // Search for rental units by feature
    $stmt = $conn->prepare("SELECT rental_id, title, description, features, price FROM rental WHERE features LIKE ?");
    $like_feature = '%' . $features . '%';
    $stmt->bind_param("s", $like_feature);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Search Results</h2>";
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Title</th>
                <th>Description</th>
                <th>Features</th>
                <th>Price</th>
                <th>Action</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['features']) . "</td>";
            echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
            // Add Review link with rental_id as a URL parameter
            echo "<td><a href='add_review.php?rental_id=" . $row['rental_id'] . "'>Add Review</a></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No rental units found with the feature: " . htmlspecialchars($features);
    }

    $stmt->close();
    $conn->close();
}
?>
