<?php include "navbar.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .search-grid .form-group {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$customer_id = $_SESSION['customer_id'];

$rental_id = isset($_GET['rental_id']) ? $_GET['rental_id'] : "";
$license_plate = isset($_GET['license_plate']) ? $_GET['license_plate'] : "";
$brand = isset($_GET['brand']) ? $_GET['brand'] : "";
$model = isset($_GET['model']) ? $_GET['model'] : "";
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : "";
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : "";

$query = "
SELECT r.rental_id, c.license_plate, c.brand, c.model, r.start_date, r.end_date, r.total_cost
FROM rental_agreements r
JOIN cars c ON r.car_id = c.car_id
WHERE r.customer_id = ?
";

$filters = [];
$params = [$customer_id];
$types = "s";

if ($rental_id !== "") { $query .= " AND r.rental_id = ?"; $types .= "i"; $params[] = $rental_id; }
if ($license_plate !== "") { $query .= " AND c.license_plate LIKE ?"; $types .= "s"; $params[] = "%$license_plate%"; }
if ($brand !== "") { $query .= " AND c.brand LIKE ?"; $types .= "s"; $params[] = "%$brand%"; }
if ($model !== "") { $query .= " AND c.model LIKE ?"; $types .= "s"; $params[] = "%$model%"; }
if ($start_date !== "") { $query .= " AND r.start_date = ?"; $types .= "s"; $params[] = $start_date; }
if ($end_date !== "") { $query .= " AND r.end_date = ?"; $types .= "s"; $params[] = $end_date; }

$stmt = $mysqli->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

    <div class="search-section">
        <h3>Search Your Rentals</h3>
        <form method="get" action="">
            <div class="search-grid">
                <div class="form-group">
                    <label>Rental ID:</label>
                    <input type="number" name="rental_id" value="<?php echo htmlspecialchars($rental_id); ?>">
                </div>

                <div class="form-group">
                    <label>License Plate:</label>
                    <input type="text" name="license_plate" value="<?php echo htmlspecialchars($license_plate); ?>">
                </div>

                <div class="form-group">
                    <label>Brand:</label>
                    <input type="text" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
                </div>

                <div class="form-group">
                    <label>Model:</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($model); ?>">
                </div>

                <div class="form-group">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>

                <div class="form-group">
                    <label>End Date:</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
            </div>
            <button type="submit">Search</button>
        </form>
    </div>

    <h3>Your Rentals</h3>
    <table>
    <thead>
    <tr>
        <th>Rental ID</th>
        <th>License Plate</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Total Cost</th>
    </tr>
    </thead>
    <tbody>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['rental_id']; ?></td>
                <td><?php echo $row['license_plate']; ?></td>
                <td><?php echo $row['brand']; ?></td>
                <td><?php echo $row['model']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td>$<?php echo number_format($row['total_cost'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7" style="text-align:center; color: #95a5a6;"><em>No rentals found.</em></td></tr>
    <?php endif; ?>
    </tbody>
    </table>
</div>

<?php
$stmt->close();
?>

</body>
</html>