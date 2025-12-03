<?php
$host = "localhost";
$user = "root";   // change if you have MySQL user
$pass = "";       // add password if required
$db   = "user_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
