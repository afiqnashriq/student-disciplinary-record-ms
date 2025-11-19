<?php
session_start();

// ✅ Use absolute paths for includes
require __DIR__ . '/../db_connects.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';
require __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Create PHPMailer instance
$mail = new PHPMailer(true);

try {
  // ✅ SMTP configuration
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'your_email';       // Your Gmail
  $mail->Password = 'your_app_password';        // Your NEW App Password
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  // ✅ Sender and recipient
  $mail->setFrom('afiqnashriq03@gmail.com', 'UPTM Disciplinary System');

  // ✅ Validate recipient email
  if (!isset($_SESSION['email']) || !filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Invalid or missing recipient email.');
  }

  $mail->addAddress($_SESSION['email']);

  // ✅ Email content
  $mail->isHTML(true);
  $mail->Subject = 'Your 2FA Verification Code';
  $mail->Body = "
    Hi {$_SESSION['username']},<br><br>
    Your 2FA verification code is: <strong>{$_SESSION['2fa_code']}</strong><br><br>
    This code will expire in 5 minutes.<br><br>
    Regards,<br>
    UPTM Disciplinary System
  ";

  // ✅ Send the email
  $mail->send();

} catch (Exception $e) {
  error_log("2FA Email Error: " . $mail->ErrorInfo);
  echo "<script>alert('2FA email failed. Please check your SMTP settings or app password.');</script>";
}
