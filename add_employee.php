<?php
$mysqli = new mysqli("localhost","root","","cs331");
$message = "";

// Fetch branches for dropdown
$branches = $mysqli->query("SELECT branch_id, city, address FROM branches");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch_id = $_POST['branch_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role = $_POST['role'];
    $salary = $_POST['salary'];

    // Validate role
    $valid_roles = ['manager','clerk','mechanic'];
    if(!in_array($role, $valid_roles)) {
        $message = "Invalid role selected!";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO employees (branch_id, first_name, last_name, role, salary)
                                  VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssd", $branch_id, $first_name, $last_name, $role, $salary);

        if($stmt->execute()) {
            $message = "Employee added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container">
    <h2>Add a New Employee</h2>

    <?php if($message != ""): ?>
        <div class="<?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Branch:</label>
            <select name="branch_id" required>
                <option value="">-- Select a branch --</option>
                <?php while($branch = $branches->fetch_assoc()): ?>
                    <option value="<?php echo $branch['branch_id']; ?>">
                        <?php echo $branch['city'] . " - " . $branch['address']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>

        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>

        <div class="form-group">
            <label>Role:</label>
            <select name="role" required>
                <option value="">-- Select role --</option>
                <option value="manager">Manager</option>
                <option value="clerk">Clerk</option>
                <option value="mechanic">Mechanic</option>
            </select>
        </div>

        <div class="form-group">
            <label>Salary:</label>
            <input type="number" step="0.01" name="salary" required>
        </div>

        <button type="submit">Add Employee</button>
    </form>
</div>

</body>
</html>
