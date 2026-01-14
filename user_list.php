<?php include "navbar.php"; ?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "db.php";

if ($_SESSION['customer_id'] != -1) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

$query = "SELECT username, customer_id FROM users ORDER BY username ASC";
$result = $mysqli->query($query);

$query2 = "SELECT customer_id, first_name, last_name, date_of_birth, phone, email, address FROM customers ORDER BY customer_id ASC";
$result2 = $mysqli->query($query2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User List</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Registered Users</h1>

    <h3>Users</h3>
    <table>
        <thead>
        <tr>
            <th>Username</th>
            <th>Customer ID</th>
        </tr>
        </thead>
        <tbody>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['customer_id']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h3>Customers</h3>
    <table>
        <thead>
        <tr>
            <th>Customer ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date of Birth</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
        </tr>
        </thead>
        <tbody>

        <?php if ($result2->num_rows > 0): ?>
            <?php while ($row = $result2->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['customer_id']) ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No customers found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>