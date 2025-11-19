<?php
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include '../db_connects.php';
session_start();

// ✅ Ensure student is logged in
if (!isset($_SESSION['studentID'])) {
    die("Unauthorized access.");
}
$studentID = $_SESSION['studentID'];

if (!isset($_GET['id'])) {
    die("Missing case ID.");
}
$caseID = $_GET['id'];

// ✅ Only fetch cases belonging to this student
$query = "
  SELECT dc.*, s.studentName, s.faculty, s.course, s.semester
  FROM disciplinary_cases dc
  INNER JOIN students s ON dc.studentID = s.studentID
  WHERE dc.caseID = ? AND dc.studentID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $caseID, $studentID);
$stmt->execute();
$result = $stmt->get_result();
$case = $result->fetch_assoc();

if (!$case) {
    die("Case not found or not authorized.");
}

// ✅ Build HTML (same format as admin version)
$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 12pt; }
  .header { text-align: center; margin-bottom: 20px; }
  .header h2 { margin: 0; font-size: 18pt; }
  .line { border-top: 2px solid #000; margin: 10px 0; }
  .content { margin-top: 20px; }
  .signature { margin-top: 60px; }
</style>

<div class="header">
  <h2>Universiti Poly-Tech Malaysia (UPTM)</h2>
  <p>Jalan 6/91, Taman Shamelin Perkasa, 56100 Kuala Lumpur</p>
  <p>Tel: +603-9283 6200 | Email: uptm@edu.my</p>
  <div class="line"></div>
</div>

<div class="content">
  <p><strong>Date:</strong> ' . htmlspecialchars($case['caseDate']) . '</p>
  <p><strong>To:</strong> ' . htmlspecialchars($case['studentName']) . ' (' . htmlspecialchars($case['studentID']) . ')</p>
  <p><strong>Faculty:</strong> ' . htmlspecialchars($case['faculty']) . '</p>
  <p><strong>Course:</strong> ' . htmlspecialchars($case['course']) . '</p>
  <p><strong>Semester:</strong> ' . htmlspecialchars($case['semester']) . '</p>

  <p>Dear Student,</p>

  <p>This letter serves as an official notice regarding a disciplinary case filed against you. The details of the case are as follows:</p>

  <table style="width:100%; border-collapse:collapse;">
    <tr><td><strong>Case ID:</strong></td><td>' . htmlspecialchars($case['caseID']) . '</td></tr>
    <tr><td><strong>Offense:</strong></td><td>' . htmlspecialchars($case['offenseType']) . '</td></tr>
    <tr><td><strong>Date & Time:</strong></td><td>' . htmlspecialchars($case['caseDate']) . ' at ' . htmlspecialchars($case['caseTime']) . '</td></tr>
    <tr><td><strong>Description:</strong></td><td>' . nl2br(htmlspecialchars($case['description'])) . '</td></tr>
    <tr><td><strong>Status:</strong></td><td>' . htmlspecialchars($case['status']) . '</td></tr>
  </table>

  <p>You are hereby advised to respond to this matter by contacting the Student Affairs Division within five (5) working days. Failure to do so may result in further disciplinary action.</p>

  <p>We trust that you will treat this matter with the seriousness it deserves.</p>
</div>

<div class="signature">
  <p>Sincerely,</p>
  <p><strong>Disciplinary Officer</strong><br>
  Student Affairs Division<br>
  Universiti Poly-Tech Malaysia (UPTM)</p>
</div>
';

// ✅ Generate PDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true); // allow logo/images if needed
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("case_report_" . $caseID . ".pdf", ["Attachment" => true]);
?>
