<?php
include '../db_connects.php'; // adjust path if needed

if (!isset($_GET['studentID'])) {
    echo json_encode(['success' => false]);
    exit;
}

$studentID = $_GET['studentID'];
$query = "SELECT studentName, faculty, course FROM students WHERE studentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'studentName' => $row['studentName'],
        'faculty' => $row['faculty'],
        'course' => $row['course']
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>
