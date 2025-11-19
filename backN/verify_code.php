<?php
session_start();
require '../db_connects.php';

$step = 'verify'; // default step
$error = '';

// ✅ Step 1: Verify code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
  $enteredCode = trim($_POST['code']);
  if ($enteredCode == $_SESSION['verification_code']) {
    $step = 'reset';
  } else {
    $error = "Invalid verification code.";
  }
}

// ✅ Step 2: Reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $error = "Passwords do not match.";
    $step = 'reset';
  } else {
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    if ($_SESSION['reset_role'] === 'Admin') {
      $id = $_SESSION['reset_userID'];
      $stmt = $conn->prepare("UPDATE users SET passwordHash = ? WHERE userID = ?");
      $stmt->bind_param("si", $hashed, $id);
    } else {
      $id = $_SESSION['reset_studentID'];
      $stmt = $conn->prepare("UPDATE students SET passwordHash = ? WHERE studentID = ?");
      $stmt->bind_param("si", $hashed, $id);
    }

    $stmt->execute();
    session_destroy();
    echo "<script>alert('Password reset successful'); window.location.href='../index.html';</script>";
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Verify Code & Reset Password</title>
  <link rel="icon" type="image/png" href="../relate/uptm_logo2.png">
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #004aad;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .btn {
      background-color: #004aad;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 20px;
    }

    .btn:hover {
      background-color: #003080;
    }

    .error {
      color: red;
      font-weight: bold;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="title">
      <?= $step === 'verify' ? 'Enter Verification Code' : 'Reset Your Password' ?>
    </div>

    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($step === 'verify'): ?>
      <form method="POST">
        <input type="text" name="code" placeholder="6-digit Code" required />
        <button type="submit" class="btn">Verify</button>
      </form>
    <?php else: ?>
      <form method="POST">
        <input type="password" name="password" placeholder="New Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required />
        <button type="submit" class="btn">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
