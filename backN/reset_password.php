<?php
session_start();
require '../db_connects.php';

if (!isset($_SESSION['reset_userID'])) {
  echo "<script>
    alert('Unauthorized access. Please verify your email first.');
    window.location.href = 'forgot_password.php';
  </script>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newPassword = $_POST['new_password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if ($newPassword !== $confirmPassword) {
    echo "<script>
      alert('Passwords do not match. Please try again.');
      window.location.href = 'reset_password.php';
    </script>";
    exit;
  }

  if (strlen($newPassword) < 6) {
    echo "<script>
      alert('Password must be at least 6 characters.');
      window.location.href = 'reset_password.php';
    </script>";
    exit;
  }

  $userID = $_SESSION['reset_userID'];

  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("UPDATE Users SET passwordHash = ? WHERE userID = ?");
  $stmt->bind_param("si", $hashedPassword, $userID);
  $stmt->execute();

  unset($_SESSION['reset_userID']);

  echo "<script>
    alert('Password reset successful. You may now log in.');
    window.location.href = '../index.html';
  </script>";

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - UPTM</title>
  <link rel="icon" type="image/png" href="../relate/uptm_logo2.png">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .reset-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 350px;
      text-align: center;
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
      color: #444;
    }

    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background-color: #0078D7;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #005fa3;
    }
  </style>
</head>
<body>
  <div class="reset-container">
    <h2>Reset Your Password</h2>
    <form method="POST">
      <label for="new_password">New Password</label>
      <input type="password" id="new_password" name="new_password" required>

      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit">RESET PASSWORD</button>
    </form>
  </div>
</body>
</html>
