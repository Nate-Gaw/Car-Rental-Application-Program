<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
?>
<nav>
    <a href="landing.php">Home</a>
    <a href="branches_employees.php">Branches & Employees</a>
    <a href="rent_car.php">Rent a Car</a>
    <?php if($customer_id == -1): ?>
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <a href="add_car.php">Add Car</a>
        <a href="add_employee.php">Add Employee</a>
        <a href="user_list.php">User List</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>
