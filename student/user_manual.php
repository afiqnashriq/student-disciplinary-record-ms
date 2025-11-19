<?php

// 🔐 Basic secure session setup
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
session_start();

// ✅ Check if user is logged in and has correct role
// Only allow access for users with the 'Student' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Student') {
  header('Location: ../index.html');
  exit;
}

// ✅ Optional: clear 2FA code after login
unset($_SESSION['2fa_code'], $_SESSION['2fa_expiry']);

// IMPORTANT: This file assumes db_connects.php exists and defines $conn
// Note: database connection and student-specific logic are structural but not strictly needed for the static manual content itself.
include '../db_connects.php';

// Keeping the function signature for structural integrity
function getStudentInfo($conn) {
    return [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student User Manual - UPTM System</title>
  <link rel="icon" type="image/png" href="../relate/uptm_logo2.png">
  <style>
    /* Global Styles (kept from original) */
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 0;
      color: #333;
    }

    /* Navbar (kept from original) */
    .navbar {
      background: linear-gradient(to right, #a678d8, #d8b4f8);
      padding: 18px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .nav-title {
      font-size: 22px;
      font-weight: 600;
      color: white;
      letter-spacing: 0.5px;
    }

    .nav-buttons button {
      background-color: white;
      color: #5a2d82;
      border: none;
      padding: 10px 18px;
      margin-left: 12px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .nav-buttons button:hover {
      background-color: #f0e6ff;
      transform: translateY(-2px);
    }

    /* New Manual Structure Styles */
    .manual-content-wrapper {
      display: flex;
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
      gap: 30px;
    }

    /* Manual Navigation Sidebar */
    .manual-nav {
      flex: 0 0 250px; /* Fixed width sidebar */
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      height: fit-content;
      position: sticky;
      top: 20px; /* Sticky effect below the navbar */
    }

    .manual-nav h3 {
      font-size: 18px;
      color: #5a2d82;
      margin-top: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #e0e0e0;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .manual-nav button {
      display: block;
      width: 100%;
      text-align: left;
      background-color: transparent;
      border: none;
      padding: 12px 15px;
      margin-bottom: 8px;
      border-radius: 8px;
      color: #333;
      font-size: 15px;
      cursor: pointer;
      transition: background-color 0.2s, color 0.2s;
    }

    .manual-nav button:hover {
      background-color: #f0e6ff;
      color: #5a2d82;
    }

    .manual-nav button.active {
      background-color: #a678d8;
      color: white;
      font-weight: 600;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Main Content Area */
    .manual-content-display {
      flex-grow: 1;
      background-color: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      min-height: 500px;
    }

    .manual-content-display h1 {
      font-size: 28px;
      color: #5a2d82;
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 10px;
      margin-bottom: 30px;
    }

    .manual-content-display h2 {
      font-size: 22px;
      color: #a678d8;
      margin-top: 30px;
      margin-bottom: 15px;
    }

    .manual-content-display p {
      line-height: 1.7;
      margin-bottom: 20px;
    }

    .manual-content-display ul {
      margin-left: 20px;
      padding-left: 0;
      list-style-type: disc;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <a href="student_dashboard.php" style="text-decoration: none;"><div class="nav-title">UPTM Discipline Management System</div></a>
  <div class="nav-buttons">
    <button onclick="location.href='student_user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<!-- Main Content: Student User Manual -->
<div class="manual-content-wrapper">

  <!-- Manual Navigation Sidebar -->
  <div class="manual-nav">
    <h3>Manual Sections</h3>
    <button class="nav-btn active" id="intro-btn">Introduction & Overview</button>
    <button class="nav-btn" id="dashboard-btn">My Cases Dashboard</button>
  </div>

  <!-- Manual Content Display Area -->
  <div class="manual-content-display" id="manual-content">
    <!-- Content will be injected here by JavaScript -->
  </div>
</div>


<script>
  // 1. Define the content for each section
  const manualSections = {
    'intro-btn': {
      title: 'Introduction to the UPTM Discipline Management System Manual (Student)',
      content: `
        <p>Welcome to the Student User Manual for the UPTM Discipline Management System. This platform allows you to securely access and review the records of any disciplinary cases reported against you.</p>

        <p>Your access is **read-only**, meaning you can view all reported information but cannot report new incidents, update existing case details, or alter any records. This platform serves as a transparency tool.</p>

        <h2>Access Point</h2>
        <p>Your only primary page is the **My Cases Dashboard**, which displays all relevant information immediately upon logging in.</p>
      `
    },
    'dashboard-btn': {
      title: 'My Cases Dashboard: Reviewing Your Records',
      content: `
        <p>The **My Cases Dashboard** is where you can view a chronological list of all disciplinary actions recorded under your Student ID. If you have no cases, a congratulatory message will be displayed.</p>

        <h2>Case Table Details</h2>
        <p>If you have recorded cases, the table will display the following five columns of information:</p>
        <ul>
          <li><strong>Case ID:</strong> A unique tracking number assigned to the specific incident.</li>
          <li><strong>Offense Type:</strong> The general category of the infraction (e.g., Inappropriate Attire, Academic Misconduct).</li>
          <li><strong>Date:</strong> The exact date the incident occurred.</li>
          <li><strong>Status:</strong> The current state of the case:
            <ul>
                <li>**Open:** The case is currently under investigation or awaiting resolution/action.</li>
                <li>**Closed:** The case has been fully resolved, and any required disciplinary action has been applied.</li>
            </ul>
          </li>
          <li><strong>Description:</strong> The detailed, objective narrative of the incident as reported by the staff member.</li>
            <li><strong>Actions:</strong> Students can click the Download PDF button to generate an official disciplinary notice.
This PDF includes key details such as the case ID, offense description, and current status — serving as an official document for recordkeeping or appeal purposes.</li>
        </ul>

        <h2>No Case Message</h2>
        <p>If the system reports <strong>"Great work! You don't have any disciplinary cases!"</strong>, it means that no incidents have been officially reported and recorded against your student record.</p>

        <h2>Support</h2>
        <p>If you believe there is an error in your record or require further clarification on a listed case, please contact the UPTM Administration office directly. The system does not support direct communication or appeal functions.</p>
      `
    }
  };

  const contentArea = document.getElementById('manual-content');
  const navButtons = document.querySelectorAll('.nav-btn');

  // 2. Function to load content
  function loadContent(sectionId) {
    const section = manualSections[sectionId];
    if (section) {
      contentArea.innerHTML = `<h1>${section.title}</h1>${section.content}`;
    }
  }

  // 3. Function to handle button clicks
  function handleNavClick(event) {
    const clickedButton = event.currentTarget;
    const sectionId = clickedButton.id;

    // Remove 'active' class from all buttons
    navButtons.forEach(btn => btn.classList.remove('active'));

    // Add 'active' class to the clicked button
    clickedButton.classList.add('active');

    // Load the corresponding content
    loadContent(sectionId);
  }

  // 4. Attach event listeners
  navButtons.forEach(button => {
    button.addEventListener('click', handleNavClick);
  });

  // 5. Load the initial content (Introduction) on page load
  window.onload = function() {
    loadContent('intro-btn');
  };
</script>

</body>
</html>