<?php

// 🔐 Basic secure session setup
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
session_start();

// ✅ Check if user is logged in and has correct role
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
  header('Location: ../index.html');
  exit;
}

// ✅ Optional: clear 2FA code after login
unset($_SESSION['2fa_code'], $_SESSION['2fa_expiry']);

include '../db_connects.php';


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
  header('Location: ../index.html');
  exit;
}


function getDashboardMetrics($conn) {
    
    return []; 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Manual – UPTM System</title>
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

<!-- Navbar (kept from original) -->
<div class="navbar">
  <a href="admin_dashboard.php" style="text-decoration: none;"><div class="nav-title">UPTM Discipline Management System</div></a>
  <div class="nav-buttons">
    <button onclick="location.href='report_case.php'">Report New Case</button>
    <button onclick="location.href='view_case.php'">View Case</button>
    <button onclick="location.href='view_staff.php'">View Staff</button>
    <button onclick="location.href='user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<!-- Main Content: User Manual -->
<div class="manual-content-wrapper">

  <!-- Manual Navigation Sidebar -->
  <div class="manual-nav">
    <h3>Manual Sections</h3>
    <button class="nav-btn active" id="intro-btn">Introduction & Overview</button>
    <button class="nav-btn" id="report-case-btn">Report New Case Page</button>
    <button class="nav-btn" id="view-case-btn">View Case Page</button>
    <button class="nav-btn" id="view-staff-btn">View Staff Page</button>
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
      title: 'Introduction to the UPTM Discipline Management System Manual',
      content: `
        <p>Welcome to the User Manual for the UPTM Discipline Management System. This system is designed to streamline the process of reporting, tracking, and managing disciplinary cases involving students. As an Administrator, this tool provides you with comprehensive oversight and the necessary functions to maintain order and accountability.</p>

        <p>This manual provides step-by-step guidance on key functionalities. Please use the navigation panel on the left to select the specific section you wish to review. Ensure you are logged in with the correct credentials to access all features described herein.</p>

        <h2>System Overview</h2>
        <p>The system is divided into three primary modules accessible from the navigation bar:</p>
        <ul>
          <li><strong>Case Management:</strong> Reporting new incidents and reviewing all active or historical cases.</li>
          <li><strong>Staff Management:</strong> Viewing and managing staff/admin users within the system.</li>
          <li><strong>Reporting & Audit:</strong> Accessing high-level metrics and system logs (covered in dashboard features).</li>
        </ul>
      `
    },
    'report-case-btn': {
      title: 'Reporting a New Disciplinary Case',
      content: `
        <p>The "Report New Case" page is the primary tool for documenting and initiating a disciplinary action against a student. Complete and accurate information is crucial for proper investigation and record-keeping.</p>

        <h2>1. Student Identification (Auto-Populate)</h2>
        <p>This section ensures the reported case is correctly linked to the student's academic record.</p>
        <ul>
          <li><strong>Student ID:</strong> Enter the student's unique ID number.</li>
          <li><strong>Auto-Fill:</strong> Once you leave the **Student ID** field (by clicking or tabbing away), the system automatically attempts to fetch and populate the **Student Name, Faculty, and Course** from the database.</li>
          <li><em>Note:</em> If the student ID is not found, an alert will be displayed, and the fields will remain blank. Double-check the ID before proceeding.</li>
        </ul>

        <h2>2. Incident Details</h2>
        <p>Provide specific details about the nature and timing of the incident.</p>
        <ul>
          <li><strong>Type of Offense:</strong> Select the category that best fits the incident from the dropdown list (e.g., Inappropriate Attire, Disruptive Behavior, Sticker Vehicle, Hairstyle).</li>
          <li><strong>Date of Incident:</strong> Use the date picker to log the exact day the incident occurred.</li>
          <li><strong>Time of Incident:</strong> Specify the time of the incident.</li>
          <li><strong>Description:</strong> Provide a detailed, objective narrative of the incident. Include the location, actions observed, and any relevant context. This is a mandatory field.</li>
        </ul>

        <h2>3. Evidence and Submission</h2>
        <p>Attach supporting documents and finalize the report.</p>
        <ul>
          <li><strong>Upload Evidence:</strong> Click "Choose File" to upload supporting evidence such as images (JPG, PNG) or documents (PDF). This step is optional but highly recommended.</li>
          <li><strong>Submission:</strong> Click the **SUBMIT** button.
            <ul>
              <li><strong>Success:</strong> You will receive a confirmation alert and be automatically redirected to the **View Case** page. The new case will be created with the status **'open'**.</li>
              <li><strong>Failure:</strong> If submission fails due to a database or file upload error, an alert will notify you.</li>
            </ul>
          </li>
        </ul>
      `
    },
    'view-case-btn': {
      title: 'Viewing and Managing Existing Cases',
      content: `
        <p>The "View Case" page is the central hub for tracking the status and details of all disciplinary cases. The table lists cases ordered by the date reported (newest first).</p>

        <h2>1. Search and Filtering</h2>
        <p>Use the controls above the table to quickly locate specific cases:</p>
        <ul>
          <li><strong>Search Input:</strong> Use the text box to perform a live search based on the **Student ID**.</li>
          <li><strong>Status Filter:</strong> Use the dropdown menu to filter the list to show only cases that are **'Open Only'** or **'Closed Only'**. Select **'All Status'** to view every case.</li>
        </ul>

        <h2>2. Case Table Details</h2>
        <p>The main table provides essential summary information for quick review:</p>
        <ul>
          <li><strong>Case ID:</strong> The unique identifier for the case.</li>
          <li><strong>Student ID:</strong> The reported student's ID (used for searching).</li>
          <li><strong>Offense Type:</strong> The category of the infraction.</li>
          <li><strong>Date:</strong> The date the incident occurred.</li>
          <li><strong>Status:</strong> The current status (open or closed).</li>
        </ul>

        <h2>3. Actions</h2>
        <p>The **Actions** column provides buttons to manage the case lifecycle:</p>
        <ul>
          <li><button style="padding: 5px 10px; background-color: #ffc107; color: #333; border: none; border-radius: 4px; font-weight: bold; cursor: default;">Update</button>: Click to go to the <code>update_case.php</code> page to modify case details, change the status, or add resolution notes.</li>
          <li><button style="padding: 5px 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: default;">Download Report</button>: Click to generate and download a formal PDF report of the case details (via <code>generate_pdf.php</code>).</li>
          <li><button style="padding: 5px 10px; background-color: #0078D7; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: default;">View Record</button>: Click to see a detailed, comprehensive view of the entire case file (via <code>view_record.php</code>).</li>
          <li><button style="padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: default;">Delete</button>: Click to permanently remove the case record from the system. **Warning:** This action requires confirmation (via a browser prompt) and cannot be undone.</li>
        </ul>
      `
    },
    'view-staff-btn': {
      title: 'Viewing and Managing Staff Records',
      content: `
        <p>The "View Staff" page provides administrative oversight into all user accounts within the Discipline Management System. This includes both staff members and other administrators. Staff entries are ordered by the creation date (newest first).</p>

        <h2>1. Staff Table Details</h2>
        <p>The table displays the following information for all system users:</p>
        <ul>
          <li><strong>User ID:</strong> The unique identifier for the user.</li>
          <li><strong>Username:</strong> The user's login name.</li>
          <li><strong>Email:</strong> The user's registered email address.</li>
          <li><strong>Role:</strong> The assigned access level (e.g., 'Admin', 'Staff').</li>
          <li><strong>Created At:</strong> The date and time the user account was created.</li>
          <li><strong>Status (Admin Only):</strong> Visible only to users with the 'Admin' role, showing if the account is **Active** or **Inactive**.</li>
        </ul>

        <h2>2. Administrative Actions</h2>
        <p>Administrative functions are available to manage user accounts:</p>
        <ul>
          <li><button style="padding: 5px 10px; background-color: #ffc107; color: #333; border: none; border-radius: 4px; font-weight: bold; cursor: default;">Update</button>: Redirects to the <code>edit_staff.php</code> page where you can modify user details, such as their username, email, or role.</li>
          <li><button style="padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: default;">Delete</button>: Click to permanently remove the staff account from the system. **Warning:** This action requires confirmation (via a browser prompt).</li>
          <li><strong>Status Toggle (Admin Only):</strong> A button visible in the Status column that allows an Admin to instantly switch a user's status between **Active** and **Inactive** (via <code>toggle_status.php</code>). This effectively enables or disables their system access.</li>
        </ul>
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