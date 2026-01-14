<?php
session_start();
require "config.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT customer_id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashedPassword)) {
        $_SESSION["customer_id"] = $id;
        $_SESSION["username"] = $username;
        header("Location: landing.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>

<div class="container">
    <h2>Login To Group 5's Car Rental System</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>

        <button type="submit">Login</button>
    </form>

    <p><?php echo $message; ?></p>

    <a href="register.php">Create an account</a>
</div>

</body>
</html>