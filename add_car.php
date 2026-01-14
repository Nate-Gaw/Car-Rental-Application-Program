<?php
$mysqli = new mysqli("localhost","root","","cs331");
$message = "";

// Fetch branches for dropdown
$branches = $mysqli->query("SELECT branch_id, city, address FROM branches");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch_id = $_POST['branch_id'];
    $license_plate = $_POST['license_plate'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $category = $_POST['category'];
    $year_manufacture = $_POST['year_manufacture'];
    $rental_status = $_POST['rental_status'];

    // Validate category and rental_status
    $valid_categories = ['SUV','sedan','hatchback'];
    $valid_statuses = ['available','rented','under_maintenance'];

    if(!in_array($category, $valid_categories)) {
        $message = "Invalid category selected!";
    } elseif(!in_array($rental_status, $valid_statuses)) {
        $message = "Invalid rental status selected!";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO cars 
            (branch_id, license_plate, brand, model, category, year_manufacture, rental_status)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssis", $branch_id, $license_plate, $brand, $model, $category, $year_manufacture, $rental_status);

        if($stmt->execute()) {
            $message = "Car added successfully!";
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
    <title>Add Car</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container">
    <h2>Add a New Car</h2>

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
            <label>License Plate:</label>
            <input type="text" name="license_plate" required>
        </div>

        <div class="form-group">
            <label>Brand:</label>
            <input type="text" name="brand" required>
        </div>

        <div class="form-group">
            <label>Model:</label>
            <input type="text" name="model" required>
        </div>

        <div class="form-group">
            <label>Category:</label>
            <select name="category" required>
                <option value="">-- Select category --</option>
                <option value="SUV">SUV</option>
                <option value="sedan">Sedan</option>
                <option value="hatchback">Hatchback</option>
            </select>
        </div>

        <div class="form-group">
            <label>Year of Manufacture:</label>
            <input type="number" name="year_manufacture" required>
        </div>

        <div class="form-group">
            <label>Rental Status:</label>
            <select name="rental_status" required>
                <option value="">-- Select status --</option>
                <option value="available">Available</option>
                <option value="rented">Rented</option>
                <option value="under_maintenance">Under Maintenance</option>
            </select>
        </div>

        <button type="submit">Add Car</button>
    </form>
</div>

</body>
</html>
