<?php include "navbar.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
$mysqli = new mysqli("localhost","root","","cs331");

// Show all cars with rental & maintenance info
$query = "SELECT c.car_id, c.license_plate, c.brand, c.model, c.category, c.rental_status,
                 r.start_date, r.end_date,
                 m.maintenance_date, m.description, m.cost
          FROM cars c
          LEFT JOIN rental_agreements r ON c.car_id = r.car_id AND r.end_date >= CURDATE()
          LEFT JOIN maintenance_records m ON c.car_id = m.car_id
          ORDER BY c.car_id";

$result = $mysqli->query($query);
?>

<div class="container">
    <h2>Admin Dashboard: Cars and Status</h2>

    <table>
    <thead>
    <tr>
        <th>Car</th><th>Category</th><th>Status</th><th>Rental Start</th><th>Rental End</th><th>Last Maintenance</th><th>Maintenance Cost</th><th>Description</th>
    </tr>
    </thead>
    <tbody>

    <?php
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['brand']} {$row['model']} ({$row['license_plate']})</td>
                <td>{$row['category']}</td>
                <td>{$row['rental_status']}</td>
                <td>{$row['start_date']}</td>
                <td>{$row['end_date']}</td>
                <td>{$row['maintenance_date']}</td>
                <td>{$row['cost']}</td>
                <td>{$row['description']}</td>
              </tr>";
    }
    ?>
    </tbody>
    </table>
</div>

</body>
</html>