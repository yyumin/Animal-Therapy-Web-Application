<?php
session_start();

// used for examine errors
// ini_set('display_errors', 1);
// error_reporting(E_ALL);


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


if(isset($_POST['submit'])) {
    $microchipNumber = $_POST['MicrochipNumber'];
    $birthDate = $_POST['BirthDate'];
    $healthStatus = $_POST['HealthStatus'];
    $species = $_POST['Species'];
    //calculate the age in years
    $birthDateObject = new DateTime($birthDate);
    $currentDateObject = new DateTime('now');
    $age = $currentDateObject->diff($birthDateObject)->y;
    $temperament = $_POST['Temperament'];
    $name = $_POST['Name'];
    $originID = $_POST['OriginID'];
    $therapyCertificaiton = $_POST['TherapyCertification'];

    // Check if MicrochipNumber already exists
    $checkStmt = $conn->prepare("SELECT MicrochipNumber FROM Animal_From WHERE MicrochipNumber = ?");
    $checkStmt->bind_param("i", $microchipNumber);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
    // MicrochipNumber already exists
    $_SESSION['message'] = "Microchip Number already exists!";
    $_SESSION['message_type'] = 'error'; 
    } else {

// when microchip number does not exist 

    // insert into Animal_From table
    // Use prepared statements to prevent SQL Injection
// SQL Query: INSERT
    $stmt = $conn->prepare("INSERT INTO Animal_From(MicrochipNumber,BirthDate,HealthStatus,Species, Age, Temperament, Name, OriginID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssissi", $microchipNumber,$birthDate,$healthStatus, $species, $age, $temperament, $name, $originID); 

    if ($stmt->execute()) {
        echo "New animal  with microchip#: " . $microchipNumber . " inserted successfully<br>";
    } else {
        // This will tell you the exact error in the SQL statement or connection
        echo "Error: " . $stmt->error;
    }
    $stmt->close();

    // insert into TherapyAnimal table
    $stmt = $conn->prepare("INSERT INTO TherapyAnimal (MicrochipNumber,TherapyCertification) VALUES (?, ?)");
    $stmt->bind_param("ii", $microchipNumber,$therapyCertificaiton); 
    if ($stmt->execute()) {
        echo "New therapy animal  with microchip#: " . $microchipNumber . " inserted successfully<br>";
    } else {
        // This will tell you the exact error in the SQL statement or connection
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$checkStmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Therapy Animal</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <div class="container mt-3">
        <div class="text-center mb-4">
            <h3 class="text-muted">Complete the form below to add a new therapy animal</h3>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] === 'error' ? 'danger' : 'success'; ?>" role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php 
        // Clear the message after displaying it
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
        <?php endif; ?>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
        <!-- <div class="row mb-3"> -->
            <div class="mb-3">
                <label class="form-label">Microchip Number:</label>
                <input type="number" class="form-control" name="MicrochipNumber"
                placeholder="Enter up to 4 digit, e.g., 5026">
            </div>

            <div class="mb-3">
                <label for="OriginID" class="form-label">Origin:</label>
                <select class="form-select" id="OriginID" name="OriginID" required>
                    <option value="">Select Origin</option>
                    <?php foreach ($origins as $origin): ?>
                    <option value="<?php echo htmlspecialchars($origin['OriginID']); ?>">
                    <?php echo htmlspecialchars($origin['Name']) . " (ID: " . htmlspecialchars($origin['OriginID']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" class="form-control" name="Name"
                placeholder="Captain Carrot">
            </div>
        <!-- </div> -->

        <div class="mb-3">
        <label class="form-label">BirthDate:</label>
          <input type="date" class="form-control" name="BirthDate" />
        </div>

        <!-- <div class="mb-3">
            <label class="form-label">Species:</label>
            <select id="Species" name="Species">
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Rabbit">Rabbit</option>
                <option value="Horse">Horse</option>
                <option value="Capybara">Capybara</option>
            </select>
        </div> -->

        <div class="mb-3">
            <label for="Species" class="form-label">Species:</label>
            <select class="form-select" id="Species" name="Species" required>
                <option value="">Select Species</option>
                <?php foreach ($speciesOptions as $species): ?>
                    <option value="<?php echo htmlspecialchars($species); ?>">
                <?php echo htmlspecialchars($species); ?>
            </option>
            <?php endforeach; ?>
            </select>
        </div>  


        <div class="mb-3">
                <label class="form-label">HealthStatus:</label>
                <input type="text" class="form-control" name="HealthStatus"
                placeholder="Healthy">
            </div>
        
         <div class="mb-3">
                <label class="form-label">Temperament:</label>
                <input type="text" class="form-control" name="Temperament"
                placeholder="Energetic">
        </div>

        <div class="form-group mb-3">
    <label>Have Therapy Certification?</label>&nbsp;
    <input type="radio" class="form-check-input" name="TherapyCertification" id="certified" value="1">
    <label for="certified" class="form-input-label">Yes</label>
    &nbsp;
    <input type="radio" class="form-check-input" name="TherapyCertification" id="not_certified" value="0">
    <label for="not_certified" class="form-input-label">No</label>
    &nbsp;
</div>
        <div>
            <button type="submit" class="btn btn-success" name="submit">Save</button>
            <a href="animal_board.php" class="btn btn-danger">cancel</a>
            <a href="animal_board.php" class="btn btn-primary">Back to Animal Board</a>
        </div>
        </form>

        </div>
    </div>
</body>
</html>