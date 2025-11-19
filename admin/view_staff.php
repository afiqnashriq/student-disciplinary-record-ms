<?php

// 🔐 Basic secure session setup
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
session_start();
$isAdmin = ($_SESSION['role'] === 'Admin');
// ✅ Check if user is logged in and has correct role
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
  header('Location: ../index.html');
  exit;
}

// ✅ Optional: clear 2FA code after login
unset($_SESSION['2fa_code'], $_SESSION['2fa_expiry']);

include '../db_connects.php'; // adjust path if needed

function fetchStaff($conn) {
    $staff = [];
    $query = "SELECT userID, username, email, userRole, createdAt, status FROM users ORDER BY createdAt DESC";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $staff[] = $row;
    }

    return $staff;
}


$staffList = fetchStaff($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Staff - UPTM System</title>
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

.delete-btn {
  background-color: #dc3545;
  color: white;
}

.delete-btn:hover {
  background-color: #c82333;
}

/* Status Toggle Buttons */
.status-toggle-btn {
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  font-size: 13px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.status-toggle-btn.activate {
  background-color: #28a745;
  color: white;
}

.status-toggle-btn.activate:hover {
  background-color: #218838;
}

.status-toggle-btn.deactivate {
  background-color: #ffc107;
  color: #333;
}

.status-toggle-btn.deactivate:hover {
  background-color: #e0a800;
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
  <h2>Staff & User Directory</h2>

  <table>
<thead>
  <tr>
    <th>User ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Role</th>
    <th>Created At</th>
    <?php if ($isAdmin): ?>
      <th>Status</th>
    <?php endif; ?>
    <th>Actions</th>
  </tr>
</thead>

<?php foreach ($staffList as $staff): ?>
<tr>
  <td><?php echo htmlspecialchars($staff['userID']); ?></td>
  <td><?php echo htmlspecialchars($staff['username']); ?></td>
  <td><?php echo htmlspecialchars($staff['email']); ?></td>
  <td><?php echo htmlspecialchars($staff['userRole']); ?></td>
  <td><?php echo htmlspecialchars($staff['createdAt']); ?></td>

  <?php if ($isAdmin): ?>
    <td>
      <?php echo htmlspecialchars($staff['status']); ?>
<form method="POST" action="toggle_status.php" style="display:inline;">
  <input type="hidden" name="userID" value="<?php echo $staff['userID']; ?>">
  <input type="hidden" name="currentStatus" value="<?php echo $staff['status']; ?>">
  <button type="submit"
          class="status-toggle-btn <?php echo ($staff['status'] === 'Active') ? 'deactivate' : 'activate'; ?>">
    <?php echo ($staff['status'] === 'Active') ? 'Deactivate' : 'Activate'; ?>
  </button>
</form>

    </td>
  <?php endif; ?>

  <td class="action-buttons">
    <button class="update-btn" onclick="location.href='edit_staff.php?id=<?php echo $staff['userID']; ?>'">Update</button>
    <button class="delete-btn" onclick="confirmDelete(<?php echo $staff['userID']; ?>)">Delete</button>
  </td>
</tr>
<?php endforeach; ?>

  </table>
</div>
<script>
function confirmDelete(userID) {
  if (confirm("Are you sure you want to delete User ID " + userID + "? This action cannot be undone.")) {
    window.location.href = "delete_staff.php?id=" + userID;
  }
}
</script>

</body>
</html>
