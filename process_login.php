<?php
session_start();
require_once "config.php";
date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Get user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Save session
            $_SESSION['id'] = $row['id'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];

            // Insert login history (only store user_id, role, fullname, username, time)
            $stmt2 = $conn->prepare("
                INSERT INTO login_history (user_id, role, fullname, username, login_time)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt2->bind_param(
                "isss",
                $row['id'],
                $row['role'],
                $row['fullname'],
                $row['username']
            );
            $stmt2->execute();

            // Redirect to homepage
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: login.php");
        exit;
    }
}
?>
