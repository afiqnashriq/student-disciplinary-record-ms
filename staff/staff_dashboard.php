<?php

// 🔐 Basic secure session setup
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
session_start();

// ✅ Check if user is logged in and is Staff
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Staff') {
  header('Location: ../index.html');
  exit;
}

// ✅ Optional: clear 2FA code after login
unset($_SESSION['2fa_code'], $_SESSION['2fa_expiry']);

include '../db_connects.php';


function getDashboardMetrics($conn) {
    $metrics = [];

    // Number of Staff
    $staffQuery = "SELECT COUNT(*) AS staffCount FROM users WHERE userRole = 'Staff'";
    $staffResult = mysqli_query($conn, $staffQuery);
    $metrics['staff'] = mysqli_fetch_assoc($staffResult)['staffCount'];

    // Number of Admins
    $adminQuery = "SELECT COUNT(*) AS adminCount FROM users WHERE userRole = 'Admin'";
    $adminResult = mysqli_query($conn, $adminQuery);
    $metrics['admin'] = mysqli_fetch_assoc($adminResult)['adminCount'];

    // Number of Students
    $studentQuery = "SELECT COUNT(*) AS studentCount FROM students";
    $studentResult = mysqli_query($conn, $studentQuery);
    $metrics['students'] = mysqli_fetch_assoc($studentResult)['studentCount'];

    // Number of Cases
    $caseQuery = "SELECT COUNT(*) AS caseCount FROM disciplinary_cases";
    $caseResult = mysqli_query($conn, $caseQuery);
    $metrics['cases'] = mysqli_fetch_assoc($caseResult)['caseCount'];

    return $metrics;
}

$metrics = getDashboardMetrics($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Page – UPTM System</title>
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
  color: white;
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
  padding: 50px 20px;
  text-align: center;
}

.main-content h2 {
  font-size: 26px;
  color: #333;
  margin-bottom: 30px;
}

/* Metrics Grid */
.metrics-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
  margin-top: 20px;
}

.metric-box {
  background-color: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.08);
  width: 260px;
  transition: transform 0.2s ease, box-shadow 0.3s ease;
  border-top: 5px solid #a678d8;
}

.metric-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.12);
}

.metric-title {
  font-size: 15px;
  color: #555;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.metric-value {
  font-size: 32px;
  font-weight: bold;
  color: #0078D7;
}

  </style>
</head>
<body>

<div class="navbar">
  <a href="staff_dashboard" style="text-decoration: none;"><div class="nav-title">UPTM Discipline Management System</div></a>
  <div class="nav-buttons">
    <button onclick="location.href='report_case.php'">Report New Case</button>
    <button onclick="location.href='view_case.php'">View Case</button>
    <button onclick="location.href='user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

  <div class="main-content">
    <h2>Welcome to the Staff Dashboard!</h2>

    <div class="metrics-grid">
      <div class="metric-box">
        <div class="metric-title">NUMBER OF STAFF</div>
        <div class="metric-value"><?php echo $metrics['staff']; ?></div>
      </div>

      <div class="metric-box">
        <div class="metric-title">NUMBER OF ADMINISTRATORS</div>
        <div class="metric-value"><?php echo $metrics['admin']; ?></div>
      </div>

      <div class="metric-box">
        <div class="metric-title">NUMBER OF CASES</div>
        <div class="metric-value"><?php echo $metrics['cases']; ?></div>
      </div>

      <div class="metric-box">
        <div class="metric-title">NUMBER OF STUDENTS</div>
        <div class="metric-value"><?php echo $metrics['students']; ?></div>
      </div>
    </div>
  </div>

</body>
</html>
