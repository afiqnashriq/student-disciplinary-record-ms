<?php
session_start();
require '../db_connects.php';

if ($_SESSION['role'] !== 'Admin') {
  die("Unauthorized access.");
}

$userID = $_POST['userID'] ?? '';
$currentStatus = $_POST['currentStatus'] ?? '';

if ($userID && $currentStatus) {
  // Normalize case to match DB values
  $currentStatus = strtolower($currentStatus);
  $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

  $stmt = $conn->prepare("UPDATE users SET status = ? WHERE userID = ?");
  $stmt->bind_param("si", $newStatus, $userID);

  if ($stmt->execute()) {
    header("Location: view_staff.php");
    exit;
  } else {
    echo "Error updating status.";
  }

  $stmt->close();
  $conn->close();
} else {
  echo "Invalid request.";
}
?>
