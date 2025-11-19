<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UPTM Registration</title>
  <link rel="stylesheet" href="index.css">
  <link rel="icon" type="image/png" href="relate/uptm_logo2.png">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .logo-container {
      text-align: center;
      margin-top: 30px;
    }

    .logo-container img {
      max-width: 180px;
    }

    .login-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    .error-box {
      background-color: #f44336;
      color: #fff;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
      animation: shake 0.3s ease-in-out;
    }

    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
      100% { transform: translateX(0); }
    }

    .role-selector {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .role-selector button {
      flex: 1;
      padding: 12px;
      border: none;
      font-weight: bold;
      cursor: pointer;
      background-color: #e0e0e0;
      color: #333;
      transition: background-color 0.3s ease;
      border-radius: 6px;
      margin: 0 5px;
    }

    .role-selector button.active {
      background-color: #007bff;
      color: white;
    }

    .form-section {
      display: none;
    }

    .form-section.active {
      display: block;
    }

    form label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
      color: #555;
    }

    form input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    form button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    form button:hover {
      background-color: #0056b3;
    }
  </style>
<script>
  function showForm(role) {
    document.getElementById('admin-form').classList.remove('active');
    document.getElementById('student-form').classList.remove('active');
    document.getElementById(role + '-form').classList.add('active');

    document.getElementById('admin-btn').classList.remove('active');
    document.getElementById('student-btn').classList.remove('active');
    document.getElementById(role + '-btn').classList.add('active');

    // ✅ Adjust margin for student view
    const container = document.querySelector('.login-container');
    if (role === 'student') {
      container.style.marginTop = '500px';
    } else {
      container.style.marginTop = '40px';
    }
  }

  window.onload = function() {
    showForm('admin'); // default view
  };
</script>

</head>
<body>

  <div class="logo-container">
    <img src="relate/uptm logo.png" alt="UPTM Logo">
  </div>

  <div class="login-container">
    <h2>Register to UPTM</h2>

    <?php if (!empty($error)): ?>
      <div class="error-box">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="role-selector">
      <button type="button" id="admin-btn" onclick="showForm('admin')">Staff</button>
      <button type="button" id="student-btn" onclick="showForm('student')">Student</button>
    </div>

    <!-- Admin Registration Form -->
    <form id="admin-form" class="form-section" action="backN/register_process.php" method="POST">
      <input type="hidden" name="role" value="Staff">

      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="staff123" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="staff@example.com" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="********" required>

      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" placeholder="********" required>

      <button type="submit">Register as Staff</button>
    </form>

    <!-- Student Registration Form -->
    <form id="student-form" class="form-section" action="backN/register_process.php" method="POST">
      <input type="hidden" name="role" value="Student">

      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Username" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="student@example.com" required>

      <label for="studentId">Student ID</label>
      <input type="text" id="studentId" name="studentId" placeholder="Insert Student ID" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="********" required>

      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" placeholder="********" required>

      <label for="studentName">Full Name</label>
      <input type="text" id="studentName" name="studentName" placeholder="Insert Full Name" required>

      <label for="faculty">Faculty</label>
      <input type="text" id="faculty" name="faculty" placeholder="Insert Faculty" required>

      <label for="course">Course</label>
      <input type="text" id="course" name="course" placeholder="Insert Course" required>

      <label for="semester">Semester</label>
      <input type="number" id="semester" name="semester" min="1" max="8" required>

      <button type="submit">Register as Student</button>
    </form>
  </div>

</body>
</html>
