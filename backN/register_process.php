<?php
session_start();
include '../db_connects.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $role = $_POST["role"] ?? '';
  $username = trim($_POST["username"]);
  $password = $_POST["password"];
  $confirm_password = $_POST["confirm_password"];
  $createdAt = date("Y-m-d H:i:s");

  // Common validation
  if (empty($username) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../register.php");
    exit;
  } elseif ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: ../register.php");
    exit;
  }

  $passwordHash = password_hash($password, PASSWORD_DEFAULT);

  if ($role === "Staff") {
    $email = trim($_POST["email"]);
    $userRole = "Staff";
    $status = "Inactive";

    if (empty($email)) {
      $_SESSION['error'] = "Email is required for Staff.";
      header("Location: ../register.php");
      exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['error'] = "Invalid email format.";
      header("Location: ../register.php");
      exit;
    }

    // Check for duplicates
    $checkQuery = "SELECT userID FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $_SESSION['error'] = "Username or email already exists.";
      header("Location: ../register.php");
      exit;
    }

    // Insert Admin
    $insertQuery = "INSERT INTO users (username, email, passwordHash, userRole, createdAt, status)
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $username, $email, $passwordHash, $userRole, $createdAt, $status);

  } elseif ($role === "Student") {
    $studentId = trim($_POST["studentId"]);
    $email = trim($_POST["email"]);
    $studentName = trim($_POST["studentName"]);
    $faculty = trim($_POST["faculty"]);
    $course = trim($_POST["course"]);
    $semester = intval($_POST["semester"]);

    if (empty($studentId) || empty($email) || empty($studentName) || empty($faculty) || empty($course) || empty($semester)) {
      $_SESSION['error'] = "All student fields are required.";
      header("Location: ../register.php");
      exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['error'] = "Invalid email format.";
      header("Location: ../register.php");
      exit;
    }

    // Check for duplicates
    $checkQuery = "SELECT studentID FROM students WHERE username = ? OR studentID = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("sss", $username, $studentId, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $_SESSION['error'] = "Username, Student ID, or Email already exists.";
      header("Location: ../register.php");
      exit;
    }

    // Insert Student
    $insertQuery = "INSERT INTO students (studentID, username, email, passwordHash, studentName, faculty, course, semester)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssssssi", $studentId, $username, $email, $passwordHash, $studentName, $faculty, $course, $semester);

  } else {
    $_SESSION['error'] = "Invalid role selected.";
    header("Location: ../register.php");
    exit;
  }

  // Execute insert
  if ($stmt->execute()) {
    if($role === "Staff") {
          unset($_SESSION['error']);
    header("Location: register_success.php");
    exit;
    }
    elseif($role === "Student") {
          unset($_SESSION['error']);
          header("Location: student_register_success.php");
    exit;
    }
  } else {
    $_SESSION['error'] = "❌ Error: " . $stmt->error;
    header("Location: ../register.php");
    exit;
  }

  $stmt->close();
  $conn->close();
}
?>
