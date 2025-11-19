
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

if (!isset($_GET['id'])) {
    die("Missing case ID.");
}

$caseID = $_GET['id'];
$query = "SELECT * FROM disciplinary_cases WHERE caseID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $caseID);
$stmt->execute();
$result = $stmt->get_result();
$case = $result->fetch_assoc();

if (!$case) {
    die("Case not found.");
}
if (isset($_POST['update'])) {
    $studentID = $_POST['studentID'];
    $caseDate = $_POST['caseDate'];
    $offenseType = $_POST['offenseType'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE disciplinary_cases SET studentID=?, caseDate=?, offenseType=?, description=?, status=? WHERE caseID=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $studentID, $caseDate, $offenseType, $description, $status, $caseID);

    if ($stmt->execute()) {
        echo "<script>alert('Case updated successfully.'); window.location.href='view_case.php';</script>";
    } else {
        echo "<script>alert('Update failed.');</script>";
    }
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
  padding: 50px 30px;
}

h2 {
  text-align: center;
  margin-bottom: 35px;
  color: #333;
  font-size: 24px;
  font-weight: 600;
}

/* Form */
form {
  background: white;
  padding: 40px 30px;
  border-radius: 12px;
  max-width: 600px;
  margin: auto;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

label {
  display: block;
  margin-top: 20px;
  font-weight: 600;
  color: #555;
}

input, textarea, select {
  width: 100%;
  padding: 12px;
  margin-top: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

input:focus,
textarea:focus,
select:focus {
  border-color: #a678d8;
  outline: none;
}

input[readonly] {
  background-color: #f9f9f9;
  color: #666;
  cursor: default;
}

/* Submit Button */
button[type="submit"] {
  margin-top: 30px;
  padding: 14px;
  background-color: #0078D7;
  color: white;
  border: none;
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
  <a href="admin_dashboard.php" style="text-decoration: none; color: black;"><div class="nav-title">UPTM Discipline Management System</div></a>
  <div class="nav-buttons">
    <button onclick="location.href='report_case.php'">Report New Case</button>
    <button onclick="location.href='view_case.php'">View Case</button>
    <button onclick="location.href='view_staff.php'">View Staff</button>
    <button onclick="location.href='user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<div class="main-content">
<h2>Update Disciplinary Case</h2>
<form method="POST">
  <label>Student ID</label>
  <input type="text" name="studentID" style="cursor:pointer;" value="<?php echo htmlspecialchars($case['studentID']); ?>" readonly> 

  <label>Case Date</label>
  <input type="date" name="caseDate" value="<?php echo htmlspecialchars($case['caseDate']); ?>" required>

  <label>Offense Type</label>
  <input type="text" name="offenseType" value="<?php echo htmlspecialchars($case['offenseType']); ?>" required>

  <label>Description</label>
  <textarea name="description"><?php echo htmlspecialchars($case['description']); ?></textarea>

  <label>Status</label>
  <select name="status">
    <option value="open" <?php if ($case['status'] == 'open') echo 'selected'; ?>>open</option>
    <option value="closed" <?php if ($case['status'] == 'closed') echo 'selected'; ?>>closed</option>
  </select>

  <button type="submit" name="update">Update Case</button>
</form>
</div>

</body>
</html>
