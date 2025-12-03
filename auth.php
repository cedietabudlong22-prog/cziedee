<?php
session_start();

// Kung wala naka-login, i-redirect balik sa login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
