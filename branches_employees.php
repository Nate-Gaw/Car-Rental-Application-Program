<?php include "navbar.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        .branch-card {
            background: white;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .branch-card h3 {
            color: #667eea;
            margin-top: 0;
        }
        
        .branch-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .employees-list {
            list-style: none;
            padding: 0;
        }
        
        .employees-list li {
            padding: 0.7rem;
            background: #f8f9fa;
            margin: 0.5rem 0;
            border-radius: 4px;
            border-left: 3px solid #764ba2;
        }
    </style>
</head>
<body>

<?php
$mysqli = new mysqli("localhost","root","","cs331");

$query = "SELECT b.branch_id, b.city, b.address, b.phone, b.email,
                 e.employee_id, e.first_name, e.last_name, e.role, e.salary
          FROM branches b
          LEFT JOIN employees e ON b.branch_id = e.branch_id
          ORDER BY b.branch_id, e.first_name";

$result = $mysqli->query($query);
$branches = [];

while($row = $result->fetch_assoc()) {
    $bid = $row['branch_id'];
    if(!isset($branches[$bid])) {
        $branches[$bid] = [
            'city' => $row['city'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'employees' => []
        ];
    }
    if($row['employee_id']) {
        $branches[$bid]['employees'][] = [
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'role' => $row['role'],
            'salary' => $row['salary']
        ];
    }
}
?>

<div class="container">
    <h2>Branches and Employees</h2>

    <?php foreach($branches as $bid => $branch): ?>
        <div class="branch-card">
            <h3><?php echo $branch['city']; ?></h3>
            
            <div class="branch-info">
                <div>
                    <strong>Address:</strong><br>
                    <?php echo $branch['address']; ?>
                </div>
                <div>
                    <strong>Phone:</strong><br>
                    <?php echo $branch['phone']; ?>
                </div>
                <div>
                    <strong>Email:</strong><br>
                    <a href="mailto:<?php echo $branch['email']; ?>"><?php echo $branch['email']; ?></a>
                </div>
            </div>
            
            <h4>Staff Members (<?php echo count($branch['employees']); ?>)</h4>
            <?php if(count($branch['employees']) > 0): ?>
                <ul class="employees-list">
                    <?php foreach($branch['employees'] as $emp): ?>
                        <li>
                            <strong><?php echo $emp['first_name'] . " " . $emp['last_name']; ?></strong>
                            <br><small><?php echo ucfirst($emp['role']) . " • $" . number_format($emp['salary'], 2); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>No employees assigned to this branch.</em></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>