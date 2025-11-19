<?php
session_start();

if (!isset($_SESSION['2fa_code']) || !isset($_SESSION['2fa_expiry'])) {
  echo "<script>
    alert('Session expired or unauthorized access.');
    window.location.href = '../index.html';
  </script>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $enteredCode = $_POST['code'] ?? '';

  if (time() > $_SESSION['2fa_expiry']) {
    echo "<script>
      alert('Verification code expired. Please log in again.');
      window.location.href = '../index.html';
    </script>";
    session_destroy();
    exit;
  }

  if ($enteredCode == $_SESSION['2fa_code']) {
    // ✅ Redirect based on role
    switch ($_SESSION['role']) {
      case 'Admin':
        header('Location: ../admin/admin_dashboard.php');
        break;
      case 'Staff':
        header('Location: ../staff/staff_dashboard.php');
        break;
      case 'Student':
        header('Location: ../student/student_dashboard.php');
        break;
      default:
        header('Location: ../error.php');
    }
    exit;
  } else {
    echo "<script>
      alert('Incorrect verification code.');
      window.location.href = 'verify_2fa.php';
    </script>";
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify 2FA - UPTM</title>
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

    .verify-container {
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

    input[type="text"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
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
  <div class="verify-container">
    <h2>Enter Your 2FA Code</h2>
    <form method="POST">
      <input type="text" name="code" placeholder="6-digit code" required>
      <button type="submit">VERIFY</button>
    </form>
  </div>
</body>
</html>
