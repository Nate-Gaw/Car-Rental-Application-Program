<?php
$host = "localhost";
$user = "root";   // XAMPP default
$pass = "";       // XAMPP default (empty)
$db   = "cs331";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>