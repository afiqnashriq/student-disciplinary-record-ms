<?php
include '../db_connects.php'; // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID     = $_POST['studentID'];
    $offenseType   = $_POST['offenseType'];
    $incidentDate  = $_POST['incidentDate'];
    $incidentTime  = $_POST['incidentTime'];
    $description   = $_POST['description'];
    $createdByID   = 1; // Replace with actual staff ID from session if available

    // Handle file upload
    $evidencePath = null;
    if (!empty($_FILES['evidence']['name'])) {
        $targetDir = "../uploads/";
        $filename = basename($_FILES["evidence"]["name"]);
        $targetFile = $targetDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $targetFile)) {
            $evidencePath = $targetFile;
        }
    }

    // Insert into disciplinary_cases (only studentID, not studentName/faculty/course)
    $query = "INSERT INTO disciplinary_cases (studentID, offenseType, caseDate, caseTime, description, evidencePath, createdByID, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, 'open')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $studentID, $offenseType, $incidentDate, $incidentTime, $description, $evidencePath, $createdByID);

    if ($stmt->execute()) {
        echo "<script>alert('Case reported successfully.'); window.location.href='view_case.php';</script>";
    } else {
        echo "<script>alert('Failed to report case.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report New Case - UPTM System</title>
  <link rel="icon" type="image/png" href="../relate/uptm_logo2.png">
  <style>
    body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f5f7fa;
  margin: 0;
  padding: 0;
}

/* Navbar */
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

/* Form Container */
.form-container {
  background-color: white;
  margin: 50px auto;
  padding: 40px 30px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  width: 100%;
  max-width: 650px;
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
  font-size: 24px;
  font-weight: 600;
}

/* Form Elements */
label {
  display: block;
  margin-top: 20px;
  font-weight: 600;
  color: #555;
}

input[type="text"],
input[type="date"],
input[type="time"],
select,
textarea {
  width: 100%;
  padding: 12px;
  margin-top: 8px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

input[type="text"]:focus,
input[type="date"]:focus,
input[type="time"]:focus,
select:focus,
textarea:focus {
  border-color: #a678d8;
  outline: none;
}

input[readonly] {
  background-color: #f9f9f9;
  color: #666;
}

input[type="file"] {
  margin-top: 10px;
  font-size: 14px;
}

/* Submit Button */
button[type="submit"] {
  width: 100%;
  padding: 14px;
  margin-top: 35px;
  background-color: #0078D7;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  font-size: 15px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
  background-color: #005fa3;
}

  </style>
</head>
<body>

<div class="navbar">
  <a href="staff_dashboard.php" style="text-decoration: none;"><div class="nav-title">UPTM Discipline Management System</div></a>
  <div class="nav-buttons">
    <button onclick="location.href='report_case.php'">Report New Case</button>
    <button onclick="location.href='view_case.php'">View Case</button>
    <button onclick="location.href='user_manual.php'">User Manual</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<div class="form-container">
  <h2>Report New Case</h2>
  <form method="POST" enctype="multipart/form-data">
    <label for="studentID">Student ID</label>
    <input type="text" id="studentID" name="studentID" placeholder="e.g. AM202010">

  <label for="studentName">Student Name</label>
  <input type="text" id="studentName" name="studentName" readonly>

  <label for="faculty">Faculty</label>
  <input type="text" id="faculty" name="faculty" readonly>

  <label for="course">Course</label>
  <input type="text" id="course" name="course" readonly>


    <label for="offenseType">Type of Offense</label>
    <select id="offenseType" name="offenseType">
      <option value="">Select Offense</option>
      <option value="Inappropriate Attire">Inappropriate Attire</option>
      <option value="Disruptive Behavior">Disruptive Behavior</option>
      <option value="Sticker Vehicle">Sticker Vehicle</option>
      <option value="Hairstyle">Hairstyle</option>
    </select>

    <label for="incidentDate">Date of Incident</label>
    <input type="date" id="incidentDate" name="incidentDate">

    <label for="incidentTime">Time of Incident</label>
    <input type="time" id="incidentTime" name="incidentTime">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4" placeholder="Describe the incident..."></textarea>

    <label for="evidence">Upload Evidence (Images/PDF)</label>
    <input type="file" id="evidence" name="evidence" accept=".jpg,.jpeg,.png,.pdf">

    <button type="submit">SUBMIT</button>
  </form>
</div>
<script>
// Fetch student details when Student ID field loses focus
document.getElementById('studentID').addEventListener('blur', function() {
  const studentID = this.value.trim();
  if (studentID === '') return;

  fetch(`fetch_student.php?studentID=${encodeURIComponent(studentID)}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('studentName').value = data.studentName;
        document.getElementById('faculty').value = data.faculty;
        document.getElementById('course').value = data.course;
      } else {
        alert('Student not found.');
        document.getElementById('studentName').value = '';
        document.getElementById('faculty').value = '';
        document.getElementById('course').value = '';
      }
    })
    .catch(error => {
      console.error('Error fetching student:', error);
    });
});
</script>

</body>
</html>
