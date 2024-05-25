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

if (isset($_POST['submit'])) {
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

    $retiredDate = $_POST['RetiredDate'];
    $reasonForRetirement = $_POST['ReasonForRetirement'];
    // adoption infomation:
    $hasAdopter = $_POST['hasAdopter'];
    $contactNumber = isset($_POST['ContactNumber']) ? $_POST['ContactNumber'] : NULL;
    $adopterName = isset($_POST['AdopterName']) ? $_POST['AdopterName'] : NULL;
    $adopterAddress = isset($_POST['AdopterAddress']) ? $_POST['AdopterAddress'] : NULL;
    $recordID = isset($_POST['RecordID']) ? $_POST['RecordID'] : NULL;
    $adoptionDate = isset($_POST['AdoptionDate']) ? $_POST['AdoptionDate'] : NULL;


    // Check if MicrochipNumber already exists
    $checkStmt = $conn->prepare("SELECT MicrochipNumber FROM Animal_From WHERE MicrochipNumber = ?");
    $checkStmt->bind_param("i", $microchipNumber);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
    // MicrochipNumber already exists
    $_SESSION['message'] = "Microchip Number already exists!";
    $_SESSION['message_type'] = 'error'; 
    } 
else {
// when microchip number does not exist 

// insert into Animal_From
    $stmt = $conn->prepare("INSERT INTO Animal_From(MicrochipNumber,BirthDate,HealthStatus,Species, Age, Temperament, Name, OriginID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssissi", $microchipNumber,$birthDate,$healthStatus, $species, $age, $temperament, $name, $originID); 

    if ($stmt->execute()) {
        echo "New animal with microchip#: " . $microchipNumber . " inserted successfully<br>";
    } else {
        // This will tell you the exact error in the SQL statement or connection
        echo "Error: " . $stmt->error;
    }
    $stmt->close();

// Check if adopter information and adoption record is provided
if (isset($_POST['hasAdopter']) && $_POST['hasAdopter'] == 'yes') {
    // if ($contactNumber && $adopterName && $adopterAddress && $recordID && $adoptionDate) {
        // Insert into Adopter table
        $adopterStmt = $conn->prepare("INSERT INTO Adopter (Address, ContactNumber, AdopterName, MicrochipNumber) VALUES (?, ?, ?, ?)");
        $adopterStmt->bind_param("sssi", $adopterAddress, $contactNumber, $adopterName, $microchipNumber);
    
        if ($adopterStmt->execute()) {
            echo "Adopter information added successfully<br>";
        } else {
            echo "Error: " . $adopterStmt->error;
        }
        $adopterStmt->close();

        // insert into retired animal
        $stmt = $conn->prepare("INSERT INTO RetiredAnimal_Adopt (MicrochipNumber, RetiredDate, ReasonForRetirement, ContactNumber, AdopterName) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $microchipNumber, $retiredDate, $reasonForRetirement, $contactNumber, $adopterName);
    
        if ($stmt->execute()) {
            echo "New retired animal  with microchip#: " . $microchipNumber . " inserted successfully<br>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    
        // Insert into AdoptionRecord table
        $adoptionRecordStmt = $conn->prepare("INSERT INTO AdoptionRecord (RecordID, AdoptionDate, MicrochipNumber, ContactNumber, AdopterName) VALUES (?, ?, ?, ?, ?)");
        $adoptionRecordStmt->bind_param("isiss", $recordID, $adoptionDate, $microchipNumber, $contactNumber, $adopterName);
    
        if ($adoptionRecordStmt->execute()) {
            echo "Adoption record added successfully<br>";
        } else {
            echo "Error: " . $adoptionRecordStmt->error;
        }
        $adoptionRecordStmt->close();
    
        // Insert into Have table
        $haveStmt = $conn->prepare("INSERT INTO Have (RecordID, MicrochipNumber) VALUES (?, ?)");
        $haveStmt->bind_param("ii", $recordID, $microchipNumber);
    
        if ($haveStmt->execute()) {
            echo "Record ID saved in our system<br>";
        } else {
            echo "Error: " . $haveStmt->error;
        }
        $haveStmt->close();
    }
else {
    $contactNumber = NULL;
    $adopterName = NULL;
// if no adoption record, insert into animal only
//insert into Retired Animal [Adopter information: contact number and adopter name set to be NULL]
    $stmt = $conn->prepare("INSERT INTO RetiredAnimal_Adopt (MicrochipNumber, RetiredDate, ReasonForRetirement, ContactNumber, AdopterName) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $microchipNumber, $retiredDate, $reasonForRetirement, $contactNumber, $adopterName);

    if ($stmt->execute()) {
        echo "New retired animal  with microchip#: " . $microchipNumber . " inserted successfully<br>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    
}

$checkStmt->close();
}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Retired Animal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-3">
        <h3 class="text-center mb-4">Complete the form below to add a Retired Animal</h3>

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

        <form action="" method="post">
            <div class="mb-3">
                <label for="MicrochipNumber" class="form-label">Microchip Number:</label>
                <input type="number" class="form-control" id="MicrochipNumber" name="MicrochipNumber" placeholder="Enter up to 4 digit, e.g., 5026" required>
            </div>
            <div class="mb-3">
                <label for="BirthDate" class="form-label">Birth Date:</label>
                <input type="date" class="form-control" id="BirthDate" name="BirthDate" required>
            </div>
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
                <label for="HealthStatus" class="form-label">Health Status:</label>
                <input type="text" class="form-control" id="HealthStatus" name="HealthStatus">
            </div>
            <div class="mb-3">
                <label for="Temperament" class="form-label">Temperament:</label>
                <input type="text" class="form-control" id="Temperament" name="Temperament">
            </div>
            <div class="mb-3">
                <label for="Name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="Name" name="Name" required>
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
                <label for="RetiredDate" class="form-label">Retired Date:</label>
                <input type="date" class="form-control" id="RetiredDate" name="RetiredDate" required>
            </div>
            <div class="mb-3">
                <label for="ReasonForRetirement" class="form-label">Reason for Retirement:</label>
                <input type="text" class="form-control" id="ReasonForRetirement" name="ReasonForRetirement" required>
            </div>

            <hr>
            <div class="mb-3">
                <label>Does this animal have an adopter?</label><br>
                <input type="radio" id="adopterYes" name="hasAdopter" value="yes">
                <label for="adopterYes">Yes (If choose yes, all the Adopt Information are required)</label><br>
                <input type="radio" id="adopterNo" name="hasAdopter" value="no" checked>
                <label for="adopterNo">No</label>
            </div>

        <div id="adopterInfo" style="display:none;">

            <h5 class="mb-3">Adopt Information</h5>

            <div class="mb-3">
                <label for="ContactNumber" class="form-label">Adopter's Contact Number:</label>
                <input type="text" class="form-control" id="ContactNumber" name="ContactNumber" placeholder="(XXX) - XXX-XXXX">
            </div>
            <div class="mb-3">
                <label for="AdopterName" class="form-label">Adopter Name:</label>
                <input type="text" class="form-control" id="AdopterName" name="AdopterName">
            </div>
            <div class="mb-3">
                <label for="AdopterAddress" class="form-label">Adopter Address:</label>
                <input type="text" class="form-control" id="AdopterAddress" name="AdopterAddress">
            </div>
            <div class="mb-3">
                <label for="RecordID" class="form-label">Record ID:</label>
                <input type="number" class="form-control" id="RecordID" name="RecordID" placeholder="Enter up to 4 digit, e.g., 7001">
            </div>
            <div class="mb-3">
                <label for="AdoptionDate" class="form-label">Adoption Date:</label>
                <input type="date" class="form-control" id="AdoptionDate" name="AdoptionDate">
            </div>

        </div>
            
            <button type="submit" class="btn btn-success" name="submit">Save</button>
            <a href="animal_board.php" class="btn btn-danger">Cancel</a>
            <a href="animal_board.php" class="btn btn-primary">Back to Animal Board</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMneT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
<script>
    document.querySelectorAll('input[name="hasAdopter"]').forEach((elem) => {
        elem.addEventListener('change', function(event) {
            var value = event.target.value;
            var adopterInfo = document.getElementById('adopterInfo');
        if(value === 'yes') {
            adopterInfo.style.display = 'block';
        } else {
            adopterInfo.style.display = 'none';
        }
    });
});
</script>




</body>
</html>
