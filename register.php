<?php
include 'db.php'; // Database connection

// Initialize error message and form data variables
$error_message = "";
$username = $password = $confirm_password = $firstName = $lastName = $email = $phone = "";

// Process form data when POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form inputs
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check for duplicate entries (username, email, phone)
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $username, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result(); // Fetch the result of the query

    if ($result->num_rows > 0) {
        // Duplicate entry found, set error message
        $error_message = "Username, email, or phone already exists!";
    } else {
        // Insert user into database
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO user (username, password, firstName, lastName, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $hashedPassword, $firstName, $lastName, $email, $phone);

        if ($stmt->execute()) {
            echo "Registration successful! <a href='login.php'>Login</a>";
            $stmt->close();
            $conn->close();
            exit(); // Stop further execution
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
    </nav>
    <h2>Register</h2>

    <?php if (!empty($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

    <form action="register.php" method="POST" onsubmit="return validateForm()">
        Username: <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br>
        Password: <input type="password" id="password" name="password" required><br>
        Confirm Password: <input type="password" id="confirm_password" name="confirm_password" required><br>
        First Name: <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required><br>
        Last Name: <input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
        Phone: <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required><br>
        <button type="submit">Register</button>
    </form>

</body>
</html>
