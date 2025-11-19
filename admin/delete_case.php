<?php
include '../db_connects.php';

if (isset($_GET['id'])) {
    $caseID = intval($_GET['id']);

    $query = "DELETE FROM disciplinary_cases WHERE caseID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $caseID);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_case.php?deleted=1");
        exit();
    } else {
        echo "Error deleting case.";
    }
} else {
    echo "Invalid request.";
}
?>
