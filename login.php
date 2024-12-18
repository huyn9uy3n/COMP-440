<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
    </nav>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>

    <?php
    include 'db.php'; // Database connection

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['username'] = $username;
                header("Location: dashboard.php"); // Redirect to the dashboard
                exit();
            } else {
                echo '<script>
                    alert("Invalid password");
                    window.location.href = "login.php";
                </script>';
            }
        } else {
            echo '<script>
                    alert("Invalid username");
                    window.location.href = "login.php";
                </script>';
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>