<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $enteredCode = trim($_POST['code'] ?? '');
  $storedCode = $_SESSION['verification_code'] ?? '';

  if ($enteredCode == $storedCode) {
    // Optional: unset the code to prevent reuse
    unset($_SESSION['verification_code']);

    echo "<script>
      alert('Code verified. You may now reset your password.');
      window.location.href = 'reset_password.php';
    </script>";
  } else {
    echo "<script>
      alert('Invalid code. Please try again.');
      window.location.href = 'verify_code.php';
    </script>";
  }
}
?>
