<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "db_conn.php"; 

if (isset($_GET['MicrochipNumber'])) {
    $microchipNumber = intval($_GET['MicrochipNumber']);
// SQL Query: DELETE
    $stmt = $conn->prepare("DELETE FROM Animal_From WHERE MicrochipNumber = ?");
    $stmt->bind_param("i", $microchipNumber);

    if ($stmt->execute()) {
        // Record deleted successfully, show a message and then redirect
        echo "<script>
                alert('Record with MicrochipNumber: " . $microchipNumber . " deleted successfully.');
                window.location.href='animal_board.php';
              </script>";
    } else {
        // Handle deletion error, show a message and then redirect
        echo "<script>
                alert('Error during deletion.');
                window.location.href='animal_board.php';
              </script>";
    }

    $stmt->close();
} else {
    // Redirect if MicrochipNumber is not set
    header("Location: animal_board.php");
    exit();
}

?>

