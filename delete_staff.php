<?php
include "db_conn.php";

if (isset($_POST['delete'])) {
    $staffID = $_POST['StaffID'];

    // Prepare the delete statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM Staff WHERE StaffID = ?");
    $stmt->bind_param("i", $staffID);

    if ($stmt->execute()) {
        echo "<script>
                alert('Record deleted successfully');
                window.location.href='staff_board.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting record: " . $stmt->error . "');
                window.location.href='staff_board.php';
              </script>";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the staff information page
    header("Location: staff_board.php");
    exit;
}
?>