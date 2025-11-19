<?php
$host = 'localhost';
$dbname = 'disciplinary_record_db';
$user = 'root';       // Change if your MySQL username is different
$pass = '';           // Change if your MySQL password is set

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
