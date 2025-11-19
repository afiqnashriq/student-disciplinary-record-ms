<?php
session_start();
session_regenerate_id(true); // Prevent session fixation
require 'db_connects.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // 🔍 First check in Users table (Admin & Staff)
  $stmt = $conn->prepare("SELECT userID, username, email, passwordHash, userRole, status FROM Users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['passwordHash'])) {
      // 🚫 Check if account is inactive
      if ($user['status'] !== 'active') {
        header('Location: backN/inactive_notice.php');
        exit;
      }

      // ✅ Proceed with OTP
      $_SESSION['username'] = $user['username'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['role'] = $user['userRole'];
      $_SESSION['userID'] = $user['userID'];

      $otp = rand(100000, 999999);
      $_SESSION['2fa_code'] = $otp;
      $_SESSION['2fa_expiry'] = time() + 300; // 5 minutes

      require 'backN/2fa_mail.php';

      header('Location: backN/verify_2fa.php');
      exit;
    } else {
      echo "<script>
      alert('Incorrect Password');
      window.location.href = 'index.html?error=incorrect-password';
      </script>";
      exit;
    }

    $stmt->close();
    $conn->close();
    exit;
  }

  // 🔍 If not found in Users, check in Students table
  $stmt = $conn->prepare("SELECT studentID, username, email, passwordHash FROM students WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();

    if (password_verify($password, $student['passwordHash'])) {
      $_SESSION['username'] = $student['username'];
      $_SESSION['email'] = $student['email'];
      $_SESSION['role'] = 'Student';
      $_SESSION['studentID'] = $student['studentID'];

      $otp = rand(100000, 999999);
      $_SESSION['2fa_code'] = $otp;
      $_SESSION['2fa_expiry'] = time() + 300;

      require 'backN/2fa_mail.php';

      header('Location: backN/verify_2fa.php');
      exit;
    } else {
      echo "<script>
      alert('Incorrect Password');
      window.location.href = 'index.html?error=incorrect-password';
      </script>";
      exit;
    }
  } else {
    header('Location: index.html?error=notfound');
    exit;
  }

  $stmt->close();
  $conn->close();
}
?>
