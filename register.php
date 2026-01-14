<?php
$mysqli = new mysqli("localhost", "root", "", "cs331");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form input
    $first = $_POST["first_name"];
    $last = $_POST["last_name"];
    $dob = $_POST["date_of_birth"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // 1. Insert into CUSTOMERS table
    $sql1 = "INSERT INTO customers (first_name, last_name, date_of_birth, phone, email, address)
             VALUES (?, ?, ?, ?, ?, ?)";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param("ssssss", $first, $last, $dob, $phone, $email, $address);

    if (!$stmt1->execute()) {
        die("Customer insert failed: " . $stmt1->error);
    }

    // Get new customer_id
    $customer_id = $mysqli->insert_id;

    // 2. Insert into USERS table
    $sql2 = "INSERT INTO users (customer_id, username, password)
             VALUES (?, ?, ?)";
    $stmt2 = $mysqli->prepare($sql2);
    $stmt2->bind_param("iss", $customer_id, $username, $password);

    if (!$stmt2->execute()) {
        die("User insert failed: " . $stmt2->error);
    }

    echo "Registration successful! Your customer ID is: " . $customer_id;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>
<div class="container">
    <h2>Register</h2>

    <form method="post">
        <label>First Name:</label><br>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label><br>
        <input type="text" name="last_name" required><br><br>

        <label>Date of Birth:</label><br>
        <input type="date" name="date_of_birth"><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email"><br><br>

        <label>Address:</label><br>
        <input type="text" name="address"><br><br>

        <hr>

        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>

    <a href="login.php">Already Have An Account? Log In</a>
</div>

</body>
</html>