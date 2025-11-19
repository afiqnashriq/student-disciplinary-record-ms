<?php

// 🔐 Basic secure session setup
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
session_start();

// ✅ Check if user is logged in and has correct role
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
  header('Location: ../index.html');
  exit;
}

// ✅ Optional: clear 2FA code after login
unset($_SESSION['2fa_code'], $_SESSION['2fa_expiry']);

include '../db_connects.php';

if (isset($_GET['id'])) {
    $userID = intval($_GET['id']);
    $query = "SELECT username, email, userRole FROM users WHERE userID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['userRole'];

    $updateQuery = "UPDATE users SET username = ?, email = ?, userRole = ? WHERE userID = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "sssi", $username, $email, $role, $userID);
    mysqli_stmt_execute($updateStmt);

    header("Location: view_staff.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Staff - UPTM System</title>
  <link rel="icon" type="image/png" href="../relate/uptm_logo2.png">
  <style>
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f5f7fa;
  margin: 0;
  padding: 0;
}

/* Navbar */
.navbar {
  background: linear-gradient(to right, #a678d8, #d8b4f8);
  padding: 18px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.nav-title {
  font-size: 22px;
  font-weight: 600;
  color: white;
  letter-spacing: 0.5px;
}

.nav-buttons button {
  background-color: white;
  color: #5a2d82;
  border: none;
  padding: 10px 18px;
  margin-left: 12px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.nav-buttons button:hover {
  background-color: #f0e6ff;
  transform: translateY(-2px);
}

/* Main Content */
.main-content {
  padding: 50px 30px;
  max-width: 600px;
  margin: 50px auto;
  background-color: white;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  border-radius: 12px;
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

h2 {
  text-align: center;
  margin-bottom: 35px;
  color: #333;
  font-size: 24px;
  font-weight: 600;
}

/* Form */
form label {
  display: block;
  margin-bottom: 10px;
  font-weight: 600;
  color: #555;
}

form input,
form select {
  width: 100%;
  padding: 12px;
  margin-bottom: 20px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

form input:focus,
form select:focus {
  border-color: #a678d8;
  outline: none;
}

/* Submit Button */
button[type="submit"] {
  background-color: #0078D7;
  color: white;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-weight: bold;
  font-size: 15px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
  background-color: #005fa3;
}

  </style>
</head>
<body>

<div class="navbar">
  <a href="admin_dashboard.php" style="text-decoration: none;"><div class="nav-title">UPTM Discipline Management System</div></a>
  <div class="nav-buttons">
    <button onclick="location.href='report_case.php'">Report New Case</button>
    <button onclick="location.href='view_case.php'">View Case</button>
    <button onclick="location.href='view_staff.php'">View Staff</button>
    <button onclick="location.href='user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<div class="main-content">
  <h2>Edit Staff Details</h2>
  <form method="POST">
    <label>Username:
      <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    </label>
    <label>Email:
      <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </label>
    <label>Role:
      <select name="userRole" required>
        <option value="Admin" <?php if ($user['userRole'] === 'Admin') echo 'selected'; ?>>Admin</option>
        <option value="Staff" <?php if ($user['userRole'] === 'Staff') echo 'selected'; ?>>Staff</option>
        <option value="Student" <?php if ($user['userRole'] === 'Student') echo 'selected'; ?>>Student</option>
      </select>
    </label>
    <button type="submit">Update</button>
  </form>
</div>

</body>
</html>
