<?php
// 🔒 Prevent back navigation after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 🔐 Secure session setup
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
require '../db_connects.php';

// Ensure student is logged in
if (!isset($_SESSION['studentID'])) {
  echo "<script>
    alert('Unauthorized access. Please log in as a student.');
    window.location.href = '../index.html';
  </script>";
  exit;
}

$studentID = $_SESSION['studentID'];

// Fetch disciplinary cases for this student
$stmt = $conn->prepare("SELECT caseID, offenseType, caseDate, status, description FROM disciplinary_cases WHERE studentID = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

$cases = [];
while ($row = $result->fetch_assoc()) {
  $cases[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Cases - UPTM System</title>
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
}

h2 {
  text-align: center;
  margin-bottom: 35px;
  color: #333;
  font-size: 24px;
  font-weight: 600;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}

th, td {
  padding: 14px 18px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

th {
  background-color: #0078D7;
  color: white;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

tr:hover {
  background-color: #f9f9f9;
}

/* No Case Message */
.no-case {
  text-align: center;
  font-size: 18px;
  color: #0078D7;
  margin-top: 50px;
  font-weight: 500;
}
.download-btn {
  background-color: #28a745;   /* green background */
  color: white;                /* white text */
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  font-weight: bold;
  font-size: 13px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.download-btn:hover {
  background-color: #1e7e34;   /* darker green on hover */
  transform: translateY(-2px); /* subtle lift effect */
}

  </style>
</head>
<body>

<div class="navbar">
  <a href="student_dashboard.php" style="text-decoration: none;">
    <div class="nav-title">UPTM Discipline Management System</div>
  </a>
  <div class="nav-buttons">
    <button onclick="location.href='user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<div class="main-content">
  <h2>My Disciplinary Cases</h2>

  <?php if (count($cases) === 0): ?>
    <div class="no-case">Great work! You don't have any disciplinary cases!</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Case ID</th>
          <th>Offense Type</th>
          <th>Date</th>
          <th>Status</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
<?php foreach ($cases as $case): ?>
  <tr>
    <td><?= htmlspecialchars($case['caseID']) ?></td>
    <td><?= htmlspecialchars($case['offenseType']) ?></td>
    <td><?= htmlspecialchars($case['caseDate']) ?></td>
    <td><?= htmlspecialchars($case['status']) ?></td>
    <td><?= htmlspecialchars($case['description']) ?></td>
    <td>
  <button class="download-btn" onclick="location.href='generate_pdf.php?id=<?= $case['caseID'] ?>'">
    Download PDF
  </button>
    </td>
  </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

</body>
</html>
