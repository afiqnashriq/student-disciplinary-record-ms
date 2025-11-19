<?php
session_start();
require '../db_connects.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $verificationCode = rand(100000, 999999);

  // 🔍 Check in Users table (Admin/Staff)
  $stmt = $conn->prepare("SELECT userID, username FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['reset_userID'] = $user['userID'];
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['reset_role'] = 'Admin';

  } else {
    // 🔍 Check in Students table
    $stmt = $conn->prepare("SELECT studentID, username FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $student = $result->fetch_assoc();
      $_SESSION['reset_studentID'] = $student['studentID'];
      $_SESSION['verification_code'] = $verificationCode;
      $_SESSION['reset_role'] = 'Student';

    } else {
      echo "<script>
        alert('Email not found. Please enter a registered email.');
        window.location.href = 'forgot_password.php';
      </script>";
      exit;
    }
  }

  // 📧 Send email
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'afiqnashriq03@gmail.com';       
    $mail->Password = 'gaez nwcm bpij uoyl';          
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('afiqnashriq03@gmail.com', 'UPTM Disciplinary System');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Verification Code – Reset Password';
    $mail->Body = "
      Hi,<br><br>
      You requested to reset your password.<br>
      Your verification code is: <strong>$verificationCode</strong><br><br>
      Please enter this code on the verification page to continue.<br><br>
      Regards,<br>
      UPTM Disciplinary System
    ";

    $mail->send();
    echo "<script>
      alert('Verification code sent to $email');
      window.location.href = 'verify_code.php';
    </script>";
  } catch (Exception $e) {
    echo "<script>
      alert('Email could not be sent. Error: {$mail->ErrorInfo}');
      window.location.href = 'forgot_password.php';
    </script>";
  }

  $stmt->close();
  $conn->close();
}
?>
