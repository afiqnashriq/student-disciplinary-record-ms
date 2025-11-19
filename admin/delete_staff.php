<?php
include '../db_connects.php';

if (isset($_GET['id'])) {
    $userID = intval($_GET['id']);
    $query = "DELETE FROM users WHERE userID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userID);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_staff.php?deleted=1");
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "Invalid request.";
}
?>
