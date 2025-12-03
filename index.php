<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['fullname'])){
    header("Location: login.php");
    exit;
}
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];

// Fetch all users
$sql = "SELECT fullname, username, role, profile_image FROM users ORDER BY id ASC";
$result = $conn->query($sql);

$sqlHistory = "
    SELECT lh.role, lh.fullname, lh.username, lh.login_time, u.profile_image
    FROM login_history lh
    LEFT JOIN users u ON lh.username = u.username
    ORDER BY lh.login_time DESC
";

$resultHistory = $conn->query($sqlHistory);




?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, rgba(128,0,0,0.9), rgba(60,0,0,0.9));
    }
    .glass-box {
      background: rgba(255,255,255,0.1);
      border-radius: 20px;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.2);
      padding: 30px;
      text-align: center;
      color: white;
      width: 90%;
      max-width: 400px;
    }
    button {
      margin: 10px;
      padding: 10px 20px;
      border: none;
      border-radius: 12px;
      background: maroon;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover { background: darkred; }
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 25px;
      width: 90%;
      max-width: 700px;
      color: white;
      max-height: 80vh;
      overflow-y: auto;
      position: relative;
    }
    .close {
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 20px;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid rgba(255,255,255,0.3);
    }
    th { background: maroon; color: white; }
    
    /* Small profile thumbnails */
    img.profile {
      width: 40px; height: 40px;
      border-radius: 50%;
      object-fit: cover;
      cursor: pointer;
      transition: transform 0.2s;
    }
    img.profile:hover {
      transform: scale(1.2);
    }

    /* Create User Form (formal table design) */
    .form-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .form-table td {
      padding: 8px;
    }
    .form-table input, .form-table select {
      width: 100%;
      padding: 8px;
      border: none;
      border-radius: 6px;
      margin-top: 4px;
    }

    /* Image Modal */
    #imageModal .modal-content {
      background: rgba(0,0,0,0.9);
      padding: 20px;
      border-radius: 15px;
      max-width: 500px;
      max-height: 80vh;
      text-align: center;
    }
    #imageModal img {
      max-width: 350px;
      max-height: 350px;
      width: auto;
      height: auto;
      border-radius: 12px;
      object-fit: contain;
      box-shadow: 0 0 15px rgba(255,255,255,0.3);
    }
  </style>
</head>
<body>
  <div class="glass-box">
    <h2>WELCOME <?php echo strtoupper($role); ?>, <?php echo $fullname; ?>!</h2>
    <button onclick="document.getElementById('createModal').style.display='flex'">Create User</button>
    <button onclick="document.getElementById('usersModal').style.display='flex'">View Users</button>
    <button onclick="document.getElementById('historyModal').style.display='flex'">Login History</button>
    <a href="logout.php"><button>Logout</button></a>
  </div>

  <!-- Create User Modal -->
  <div class="modal" id="createModal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('createModal').style.display='none'">&times;</span>
      <h3>Create User</h3>
      <form action="register.php" method="POST" enctype="multipart/form-data">
        <table class="form-table">
          <tr>
            <td>Role:</td>
            <td>
              <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Full Name:</td>
            <td><input type="text" name="fullname" required></td>
          </tr>
          <tr>
            <td>Username:</td>
            <td><input type="text" name="username" required></td>
          </tr>
          <tr>
            <td>Password:</td>
            <td><input type="password" name="password" required></td>
          </tr>
          <tr>
            <td>Profile Image:</td>
            <td><input type="file" name="profile_image" accept="image/*" required></td>
          </tr>
        </table>
        <button type="submit" style="margin-top:15px;">Create Account</button>
      </form>
    </div>
  </div>

  <!-- Users Modal -->
  <!-- Users Modal -->
<div class="modal" id="usersModal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('usersModal').style.display='none'">&times;</span>
    <h3>Users List</h3>
    <input type="text" id="userSearch" onkeyup="filterTable('usersTable','userSearch')" placeholder="Search fullname or username..." style="width:100%;padding:8px;margin-bottom:10px;border-radius:6px;">
    <table id="usersTable">
      <tr><th>Profile</th><th>Full Name</th><th>Username</th><th>Role</th></tr>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if($row['profile_image']): ?>
              <img src="uploads/<?php echo htmlspecialchars($row['profile_image']); ?>" 
                   class="profile" 
                   onclick="openImageModal('uploads/<?php echo htmlspecialchars($row['profile_image']); ?>')">
            <?php else: ?><span>No Image</span><?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($row['fullname']); ?></td>
          <td><?php echo htmlspecialchars($row['username']); ?></td>
          <td><?php echo htmlspecialchars($row['role']); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>


  <!-- Login History Modal -->
<div class="modal" id="historyModal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('historyModal').style.display='none'">&times;</span>
    <h3>Login History</h3>
    <input type="text" id="historySearch" onkeyup="filterTable('historyTable','historySearch')" placeholder="Search fullname, username, or date..." style="width:100%;padding:8px;margin-bottom:10px;border-radius:6px;">
    <table id="historyTable">
      <tr><th>Profile</th><th>Role</th><th>Full Name</th><th>Username</th><th>Login Time</th></tr>
      <?php while($h = $resultHistory->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if($h['profile_image']): ?>
              <img src="uploads/<?php echo htmlspecialchars($h['profile_image']); ?>" 
                   class="profile" 
                   onclick="openImageModal('uploads/<?php echo htmlspecialchars($h['profile_image']); ?>')">
            <?php else: ?><span>No Image</span><?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($h['role']); ?></td>
          <td><?php echo htmlspecialchars($h['fullname']); ?></td>
          <td><?php echo htmlspecialchars($h['username']); ?></td>
          <td><?php echo htmlspecialchars($h['login_time']); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>


  <!-- Image Modal -->
  <div class="modal" id="imageModal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('imageModal').style.display='none'">&times;</span>
      <img id="modalImage" src="">
    </div>
  </div>

<!-- Auto Logout Warning -->
<div id="logoutWarning" style="
  display:none; 
  position:fixed; 
  bottom:20px; 
  right:20px; 
  background:rgba(0,0,0,0.8); 
  color:white; 
  padding:15px 20px; 
  border-radius:10px; 
  font-size:16px;
  z-index:1000;
">
  ⚠️ You will be logged out in <span id="countdown">10</span> seconds due to inactivity.
</div> 



  <script>
  function openImageModal(src) {
    document.getElementById("modalImage").src = src;
    document.getElementById("imageModal").style.display = "flex";
  }

  // Close modal when clicking outside
  window.onclick = function(event) {
    ["createModal","usersModal","historyModal","imageModal"].forEach(id=>{
      let modal=document.getElementById(id);
      if(event.target===modal){ modal.style.display="none"; }
    });
  }

  // Search filter function
  function filterTable(tableId, searchId) {
    let input = document.getElementById(searchId);
    let filter = input.value.toLowerCase();
    let table = document.getElementById(tableId);
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) { // start at 1 to skip header row
      let td = tr[i].getElementsByTagName("td");
      let rowMatch = false;

      for (let j = 0; j < td.length; j++) {
        if (td[j]) {
          let txtValue = td[j].textContent || td[j].innerText;
          if (txtValue.toLowerCase().indexOf(filter) > -1) {
            rowMatch = true;
            break;
          }
        }
      }

      tr[i].style.display = rowMatch ? "" : "none";
    }
  }
</script>
<!---->
<script>
  let inactivityTime = 60; // seconds before logout
  let warningTime = 10;    // show warning at 10s
  let countdown;
  let timer;

  function startTimer() {
    let timeLeft = inactivityTime;

    timer = setInterval(function() {
      timeLeft--;

      if (timeLeft <= warningTime) {
        document.getElementById("logoutWarning").style.display = "block";
        document.getElementById("countdown").innerText = timeLeft;
      }

      if (timeLeft <= 0) {
        clearInterval(timer);
        window.location.href = "logout.php"; // redirect to logout
      }
    }, 1000);

    countdown = timeLeft;
  }

  function resetTimer() {
    clearInterval(timer);
    document.getElementById("logoutWarning").style.display = "none";
    startTimer();
  }

  // Detect activity
  window.onload = startTimer;
  document.onmousemove = resetTimer;
  document.onkeydown = resetTimer;
  document.onclick = resetTimer;
</script>

</body>
</html>
