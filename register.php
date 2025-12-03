<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle profile picture upload
    $profile_image = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
            $profile_image = $fileName;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (role, fullname, username, password, profile_image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $role, $fullname, $username, $password, $profile_image);

    if ($stmt->execute()) {
        // Redirect only, no echo before header
        header("Location: index.php?success=1");
        exit;
    } else {
        // Show error if insert fails
        echo "Error: " . $stmt->error;
    }
}
?>
