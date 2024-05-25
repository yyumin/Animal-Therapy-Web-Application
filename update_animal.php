<?php
include "db_conn.php"; 

// fetch the origin ID and Name
$origins = [];
$query = "SELECT OriginID, Name FROM Origin";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $origins[] = $row;
    }
}

// fetch the distinct species
$speciesOptions = [];
$speciesQuery = "SELECT DISTINCT Species FROM Animal_From ORDER BY Species";
$speciesResult = $conn->query($speciesQuery);

if ($speciesResult->num_rows > 0) {
    while($row = $speciesResult->fetch_assoc()) {
        $speciesOptions[] = $row['Species'];
    }
}

$animal = false; // Initialize the variable to avoid undefined variable notice

// Check if 'MicrochipNumber' is set in the GET request and fetch existing data
if (isset($_GET['MicrochipNumber'])) {
    $microchipNumber = intval($_GET['MicrochipNumber']);

    // If the form has been submitted, update the animal
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Gather data from the form
        $name = $_POST['Name'];
        $birthdate = $_POST['BirthDate'];
        $healthStatus = $_POST['HealthStatus'];
        $species = $_POST['Species'];
        $age = intval($_POST['Age']);
        $temperament = $_POST['Temperament'];
        // $originID = intval($_POST['OriginID']);
        $originID = $_POST['OriginID'];

        // Prepare the UPDATE statement
// SQL Query: UPDATE
$update_stmt = $conn->prepare("UPDATE Animal_From SET Name=?, BirthDate=?, HealthStatus=?, Species=?, Age=?, Temperament=?, OriginID=? WHERE MicrochipNumber=?");
$update_stmt->bind_param("ssssisii", $name, $birthdate, $healthStatus, $species, $age, $temperament, $originID, $microchipNumber);

        // Execute the update
        if ($update_stmt->execute()) {
            // Redirect back to the animal board
            header("Location: animal_board.php");
            exit();
        } else {
            echo "Error updating record: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        // Fetch the animal's current data only if it's not a POST request
        $stmt = $conn->prepare("SELECT * FROM Animal_From WHERE MicrochipNumber = ?");
        $stmt->bind_param("i", $microchipNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $animal = $result->fetch_assoc();

        $stmt->close();
    }
} else {
    // If MicrochipNumber is not set, redirect back to the board page
    header("Location: animal_board.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Animal Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Update Animal Information</h2>
  <?php if ($animal): // Check if animal data is available ?>
  <form action="update_animal.php?MicrochipNumber=<?php echo $microchipNumber; ?>" method="post">
    <!-- Form fields -->
    <!-- ... -->
    <div class="mb-3">
      <label for="Name" class="form-label">Name:</label>
      <input type="text" class="form-control" id="Name" name="Name" value="<?php echo $animal['Name']; ?>">
    </div>
    <div class="mb-3">
      <label for="BirthDate" class="form-label">Birth Date:</label>
      <input type="date" class="form-control" id="BirthDate" name="BirthDate" value="<?php echo $animal['BirthDate']; ?>">
    </div>
    <div class="mb-3">
      <label for="HealthStatus" class="form-label">Health Status:</label>
      <input type="text" class="form-control" id="HealthStatus" name="HealthStatus" value="<?php echo $animal['HealthStatus']; ?>">
    </div>

    <div class="mb-3">
    <label for="Species" class="form-label">Species:</label>
    <select class="form-select" id="Species" name="Species" required>
        <option value="">Select Species</option>
        <?php foreach ($speciesOptions as $species): ?>
            <option value="<?php echo htmlspecialchars($species); ?>"
                <?php if ($species == $animal['Species']): ?> selected="selected"<?php endif; ?>>
                <?php echo htmlspecialchars($species); ?>
            </option>
        <?php endforeach; ?>
    </select>
    </div>


    <div class="mb-3">
      <label for="Age" class="form-label">Age:</label>
      <input type="number" class="form-control" id="Age" name="Age" value="<?php echo $animal['Age']; ?>">
    </div>
    <div class="mb-3">
      <label for="Temperament" class="form-label">Temperament:</label>
      <input type="text" class="form-control" id="Temperament" name="Temperament" value="<?php echo $animal['Temperament']; ?>">
    </div>
    <!-- <div class="mb-3">
      <label for="OriginID" class="form-label">Origin ID:</label>
      <input type="number" class="form-control" id="OriginID" name="OriginID" value="<?php echo $animal['OriginID']; ?>">
    </div> -->
    <div class="mb-3">
    <label for="OriginID" class="form-label">Origin:</label>
    <select class="form-select" id="OriginID" name="OriginID" required>
        <option value="">Select Origin</option>
        <?php foreach ($origins as $origin): ?>
            <option value="<?php echo htmlspecialchars($origin['OriginID']); ?>"
                <?php if ($origin['OriginID'] == $animal['OriginID']): ?> selected="selected"<?php endif; ?>>
                <?php echo htmlspecialchars($origin['Name']) . " (ID: " . htmlspecialchars($origin['OriginID']) . ")"; ?>
            </option>
        <?php endforeach; ?>
    </select>
    </div>


    <button type="submit" class="btn btn-primary">Update</button>
    <a href="animal_board.php" class="btn btn-danger">cancel</a>
  </form>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>