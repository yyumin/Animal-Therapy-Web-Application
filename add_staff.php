<?php
include "db_conn.php";

function isStaffIDValid($id) {
    // Add logic to validate the StaffID 
    return preg_match("/^6\d{3}$/", $id);
}

function isNameValid($name) {
    // Add logic to validate the Name
    return !preg_match("/[^A-Za-z\s]/", $name);
}

function isRoleValid($role) {
    // Add logic to validate the Role 
    return !preg_match("/[^A-Za-z\s]/", $role);
}

if (isset($_POST['submit'])) {
    $staffID = $_POST['StaffID'];
    $name = $_POST['Name'];
    $role = $_POST['Role'];
    
    // Validate input
    if (!isStaffIDValid($staffID)) {
        echo "Error: Invalid StaffID. It should be in the format 6XXX.";
    } elseif (!isNameValid($name)) {
        echo "Error: Invalid Name. Only letters and spaces are allowed.";
    } elseif (!isRoleValid($role)) {
        echo "Error: Invalid Role. Only letters and spaces are allowed.";
    } else {
         // Check if StaffID already exists in the database
         $checkStmt = $conn->prepare("SELECT * FROM Staff WHERE StaffID = ?");
         $checkStmt->bind_param("i", $staffID);
         $checkStmt->execute();
         $result = $checkStmt->get_result();
         if ($result->num_rows > 0) {
             echo "Error: StaffID already exists.";
         } else {
        // Use prepared statements to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO Staff (StaffID, Name, Role) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $staffID, $name, $role); // 'iss' denotes integer, string, string types respectively

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            if ($conn->errno == 1062) {
                echo "Error: Duplicate entry for StaffID.";
            } else {
                // This will tell you the exact error in the SQL statement or connection
                echo "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }
    $checkStmt->close();
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Application</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <div class="container mt-3">
        <div class="text-center mb-4">
            <h3 class="text-muted">Complete the form below to add a new staff</h3>
        </div>
        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
        
        <div class="mb-3">
                <label class="form-label">StaffID:</label>
                <input type="text" class="form-control" name="StaffID"
                placeholder="6XXX">
            </div>
        
         <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" class="form-control" name="Name">
        </div>

        <div class="mb-3">
                <label class="form-label">Role:</label>
                <select class="form-control" name="Role">
                  <option value="">Select a Role</option> <!-- Default option -->
                  <option value="Volunteer">Volunteer</option>
                  <option value="Intern">Intern</option>
                  <option value="Trainer">Trainer</option>
                  <option value="Assistant">Assistant</option>
                  <option value="Coordinator">Coordinator</option>
                  <option value="Supervisor">Supervisor</option>
                  <option value="Assistant">Manager</option>

    
    </select> 
        </div>
        
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-success" name="submit">Save</button>
            <a href="staff_board.php" class="btn btn-danger">Cancel</a>
        </div>
</body>
</html>