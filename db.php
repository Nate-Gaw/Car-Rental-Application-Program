<?php
$mysqli = new mysqli("localhost", "root", "", "cs331");

if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}
?>