<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$mysqli = new mysqli("localhost", "root", "", "cs331");

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit;
}

date_default_timezone_set('America/New_York');
$today = date('Y-m-d');

$update_rented = "
UPDATE cars c
JOIN rental_agreements r 
    ON c.car_id = r.car_id 
    AND r.start_date <= ? 
    AND r.end_date >= ?
SET c.rental_status = 'rented'";
$stmt = $mysqli->prepare($update_rented);
$stmt->bind_param("ss", $today, $today);
$stmt->execute();
$stmt->close();

$update_available = "
UPDATE cars c
SET c.rental_status = 'available'
WHERE c.rental_status != 'under_maintenance'
AND c.car_id NOT IN (
    SELECT car_id 
    FROM rental_agreements
    WHERE start_date <= ? AND end_date >= ?
)";
$stmt2 = $mysqli->prepare($update_available);
$stmt2->bind_param("ss", $today, $today);
$stmt2->execute();
$stmt2->close();

$cars = $mysqli->query("SELECT * FROM cars WHERE rental_status = 'available'");
$message = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $car_id = $_POST['car_id'];
    $customer_id = $_SESSION['customer_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $daily_rate = $_POST['daily_rate'];
    $payment_method = $_POST['payment_method'];
    $days = (strtotime($end_date) - strtotime($start_date))/86400 + 1;
    $total_cost = $daily_rate * $days;

    $stmt = $mysqli->prepare("SELECT rental_status FROM cars WHERE car_id = ? AND rental_status = 'available'");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows == 0){
        $message = "Sorry, this car is no longer available.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO rental_agreements (car_id, customer_id, start_date, end_date, daily_rate, total_cost) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdd", $car_id, $customer_id, $start_date, $end_date, $daily_rate, $total_cost);
        if($stmt->execute()){
            $rental_id = $stmt->insert_id;
            $stmt->close();

            $stmt2 = $mysqli->prepare("INSERT INTO payments (rental_id, amount, method) VALUES (?, ?, ?)");
            $stmt2->bind_param("ids", $rental_id, $total_cost, $payment_method);
            $stmt2->execute();
            $stmt2->close();

            $stmt3 = $mysqli->prepare("UPDATE cars SET rental_status='rented' WHERE car_id=?");
            $stmt3->bind_param("i", $car_id);
            $stmt3->execute();
            $stmt3->close();

            $message = "Rental and payment successful!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent a Car</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container">
    <h2>Rent a Car</h2>

    <?php if($message != ""): ?>
        <div class="<?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Select Car:</label>
            <select name="car_id" required>
                <option value="">-- Choose a car --</option>
                <?php 
                // Reset the result pointer
                $cars = $mysqli->query("SELECT * FROM cars WHERE rental_status = 'available'");
                while($car = $cars->fetch_assoc()): ?>
                    <option value="<?php echo $car['car_id']; ?>">
                        <?php echo $car['brand'] . " " . $car['model'] . " (" . $car['category'] . ") - License: " . $car['license_plate']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Start Date:</label>
            <input type="date" name="start_date" required>
        </div>

        <div class="form-group">
            <label>End Date:</label>
            <input type="date" name="end_date" required>
        </div>

        <div class="form-group">
            <label>Daily Rate ($):</label>
            <input type="number" step="0.01" name="daily_rate" required>
        </div>
        
        <div class="form-group">
            <label>Payment Method:</label>
            <select name="payment_method" required>
                <option value="">-- Select payment method --</option>
                <option value="credit_card">Credit Card</option>
                <option value="cash">Cash</option>
                <option value="online_transfer">Online Transfer</option>
            </select>
        </div>

        <button type="submit">Rent Car</button>
    </form>
</div>

</body>
</html>
