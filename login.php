<?php
session_start();
if(isset($_SESSION['fullname'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      margin: 0; padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, rgba(128,0,0,0.9), rgba(60,0,0,0.9));
      font-family: Arial, sans-serif;
    }
    .glass-box {
      background: rgba(255,255,255,0.1);
      border-radius: 20px;
      padding: 40px;
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      width: 90%;
      max-width: 350px;
      text-align: center;
      color: white;
    }
    .logo {
      width: 100px;
      margin-bottom: 15px;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
    }
    button {
      padding: 12px;
      width: 100%;
      background: maroon;
      border: none;
      border-radius: 12px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover { background: darkred; }
  </style>
</head>
<body>
  
  <div class="glass-box">
    <!-- Logo above login -->
    <img src="img/apmc.png" alt="Logo" class="logo">

    <h2> Please Login</h2>
    <form action="process_login.php" method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
