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

include '../db_connects.php'; // adjust path if needed
$cases = fetchDisciplinaryCases($conn);


function fetchDisciplinaryCases($conn) {
    $cases = [];

    $query = "
        SELECT dc.caseID, dc.studentID, s.studentName, dc.caseDate, dc.offenseType, dc.status
        FROM disciplinary_cases dc
        INNER JOIN students s ON dc.studentID = s.studentID
        ORDER BY dc.caseDate DESC
    ";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }

    return $cases;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Cases - UPTM System</title>
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
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 30px;
}

h2 {
  text-align: center;
  margin-bottom: 40px;
  color: #333;
  font-size: 28px;
  font-weight: 600;
}

/* Filter Bar */
.filter-bar {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-bottom: 25px;
}

.filter-bar input {
  flex: 1;
  min-width: 300px;
  padding: 12px 18px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.filter-bar select {
  width: 180px;
  padding: 12px 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  transition: border-color 0.3s ease;
}


.filter-bar input:focus,
.filter-bar select:focus {
  border-color: #a678d8;
  outline: none;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

th, td {
  padding: 16px 20px;
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

/* Action Buttons */
.action-buttons button {
  padding: 8px 14px;
  margin-right: 6px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  font-size: 13px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.update-btn {
  background-color: #ffc107;
  color: #333;
}

.update-btn:hover {
  background-color: #e0a800;
}

.view-btn {
  background-color: #0078D7;
  color: white;
}

.view-btn:hover {
  background-color: #005fa3;
}

.generate-btn {
  background-color: #28a745;
  color: white;
}

.generate-btn:hover {
  background-color: #1e7e34;
}

.delete-btn {
  background-color: #dc3545;
  color: white;
}

.delete-btn:hover {
  background-color: #c82333;
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
  <div class="filter-bar">
  <input type="text" id="searchInput" placeholder="Search by Student ID...">
  <select id="statusFilter">
    <option value="">All Status</option>
    <option value="open">Open Only</option>
    <option value="closed">Closed Only</option>
  </select>
</div>

  <h2>Current Disciplinary Cases</h2>

  <table>
    <thead>
      <tr>
        <th>Case ID</th>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Offense Type</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($cases as $case): ?>
<tr>
  <td><?php echo htmlspecialchars($case['caseID']); ?></td>
  <td><?php echo htmlspecialchars($case['studentID']); ?></td>
  <td><?php echo htmlspecialchars($case['studentName']); ?></td> <!-- new cell -->
  <td><?php echo htmlspecialchars($case['offenseType']); ?></td>
  <td><?php echo htmlspecialchars($case['caseDate']); ?></td>
  <td><?php echo htmlspecialchars($case['status']); ?></td>
  <td class="action-buttons">
    <button class="update-btn" onclick="location.href='update_case.php?id=<?php echo $case['caseID']; ?>'">Update</button>
    <button class="generate-btn" onclick="location.href='generate_pdf.php?id=<?php echo $case['caseID']; ?>'">Download Report</button>
    <button class="view-btn" onclick="location.href='view_record.php?id=<?php echo $case['caseID']; ?>'">View Record</button>
    <button class="delete-btn" onclick="confirmDelete(<?php echo $case['caseID']; ?>)">Delete</button>
  </td>
</tr>
<?php endforeach; ?>


    </tbody>
  </table>
</div>
</body>
<script>
function confirmDelete(caseID) {
  if (confirm("Are you sure you want to delete Case ID " + caseID + "? This action cannot be undone.")) {
    window.location.href = "delete_case.php?id=" + caseID;
  }
}
function confirmDelete(caseID) {
  if (confirm("Are you sure you want to delete Case ID " + caseID + "? This action cannot be undone.")) {
    window.location.href = "delete_case.php?id=" + caseID;
  }
}

// 🔍 Live Search + Filter
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
  const searchValue = document.getElementById('searchInput').value.toLowerCase();
  const statusValue = document.getElementById('statusFilter').value.toLowerCase();
  const rows = document.querySelectorAll('tbody tr');

  rows.forEach(row => {
    const studentID = row.cells[1].textContent.toLowerCase();
    const status = row.cells[5].textContent.toLowerCase(); // ✅ Status column

    const matchesSearch = studentID.includes(searchValue);
    const matchesStatus = statusValue === '' || status === statusValue;

    row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
  });
}

</script>

</html>