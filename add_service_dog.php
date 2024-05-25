<?php

session_start();  // to handle accumulated messages
// handle errors
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

include "db_conn.php";

// handle foreign keys problem
// fetch the origin ID and Name
$origins = [];
$query = "SELECT OriginID, Name FROM Origin";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $origins[] = $row;
    }
}
// fetch the Staff ID and Name
$staffMembers = [];
$staffQuery = "SELECT StaffID, Name FROM Staff"; 
$staffResult = $conn->query($staffQuery);

if ($staffResult->num_rows > 0) {
    while($row = $staffResult->fetch_assoc()) {
        $staffMembers[] = $row;
    }
}

if(isset($_POST['submit'])) {
    // initialize an array to hold success messages
    $_SESSION['success_messages'] = [];

    $microchipNumber = $_POST['MicrochipNumber'];
    $birthDate = $_POST['BirthDate'];
    $healthStatus = $_POST['HealthStatus'];
    $species = 'Dog'; // fixed value for species
    //calculate the age in years
    $birthDateObject = new DateTime($birthDate);
    $currentDateObject = new DateTime('now');
    $age = $currentDateObject->diff($birthDateObject)->y;
    $temperament = $_POST['Temperament'];
    $name = $_POST['Name'];
    $originID = $_POST['OriginID'];

    // attributes for service dog
    $serviceCertification = $_POST['ServiceCertification'];

    if ($serviceCertification === 'Yes') {
        $certificationNumber = $_POST['CertificationNumber']; // get the certification number
    } else {
        $certificationNumber = NULL; // set as NULL when not certified
    }
    
    // attributs for qualified dog
    $qualificationDate = $_POST['QualificationDate']; 
    $skillSet = $_POST['SkillSet']; 
    // attributes for unqualified dogs (default values)
$trainingStatus = isset($_POST['TrainingStatus']) ? $_POST['TrainingStatus'] : NULL;
$staffID = isset($_POST['StaffID']) && is_numeric($_POST['StaffID']) ? intval($_POST['StaffID']) : NULL; 
$startDate = isset($_POST['StartDate']) && $_POST['StartDate'] != '' ? $_POST['StartDate'] : '0000-00-00';
$daysRemaining = isset($_POST['DaysRemaining']) && is_numeric($_POST['DaysRemaining']) ? intval($_POST['DaysRemaining']) : 100;


        // Check if MicrochipNumber already exists
        $checkStmt = $conn->prepare("SELECT MicrochipNumber FROM Animal_From WHERE MicrochipNumber = ?");
        $checkStmt->bind_param("i", $microchipNumber);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($result->num_rows > 0) {
            // MicrochipNumber already exists
            header('Location: add_service_dog.php?error=MicrochipNumber already exists');
            exit;
        }
        $checkStmt->close();

    // inserting into Animal_From
    $stmt = $conn->prepare("INSERT INTO Animal_From(MicrochipNumber,BirthDate,HealthStatus,Species, Age, Temperament, Name, OriginID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssissi", $microchipNumber,$birthDate,$healthStatus, $species, $age, $temperament, $name, $originID); 
    if ($stmt->execute()) {
        $_SESSION['success_messages'][] = "New animal with microchip#: " . $microchipNumber . " added successfully.";
    } else {
        // Handle the error and exit
        header('Location: add_service_dog.php?error=Error adding new animal');
        exit;
    }
    $stmt->close();

    // insert into Sercive Dogs
    $stmt = $conn->prepare("INSERT INTO ServiceDog (MicrochipNumber, ServiceCertification) VALUES (?, ?)");
    $stmt->bind_param("is", $microchipNumber, $serviceCertification);
    if ($stmt->execute()) {
        $_SESSION['success_messages'][] = "New service dog with microchip#: " . $microchipNumber . " added successfully.";
    } else {
        header('Location: add_service_dog.php?error=Error adding service dog information');
        exit;
    }
    $stmt->close();

    if ($serviceCertification === 'Yes') {
        // Insert into QualifiedDog
        $stmt = $conn->prepare("INSERT INTO QualifiedDog (MicrochipNumber, QualificationDate, SkillSet) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $microchipNumber, $qualificationDate, $skillSet);
        if (!$stmt->execute()) {
            error_log("Error inserting into QualifiedDog: " . $stmt->error);
            header('Location: add_service_dog.php?error=Error adding qualified service dog');
            exit;
        }
        $stmt->close();
        $_SESSION['success_messages'][] = "New qualified service dog with microchip#: " . $microchipNumber . " added successfully.";
        // echo "New qualified service dog added successfully<br>";
        // header('Location: add_service_dog.php?success=New qualified service dog added successfully');
        // exit;
    } else {
        $allInsertsSuccessful = true; // Initialize the success flag

        // insert into UnqualifiedDog_Train_R1 
        $stmt1 = $conn->prepare("INSERT INTO UnqualifiedDog_Train_R1 (MicrochipNumber, TrainingStatus) VALUES (?, ?)");
        $stmt1->bind_param("is", $microchipNumber, $trainingStatus);
        if ($stmt1->execute()) {
            echo "Training Status Inserted successfully<br>";
        } else {
            $allInsertsSuccessful = false;
            echo "Error inserting into UnqualifiedDog_Train_R1: " . $stmt1->error;
        }
        $stmt1->close();

        // insert into UnqualifiedDog_Train_R2 
        $stmt2 = $conn->prepare("INSERT INTO UnqualifiedDog_Train_R2 (MicrochipNumber, StartDate) VALUES (?, ?)");
        $stmt2->bind_param("is", $microchipNumber, $startDate);
        if ($stmt2->execute()) {
            echo "training start date Inserted successfully<br>";
        } else {
            echo "Error inserting into UnqualifiedDog_Train_R2: " . $stmt2->error;
            $allInsertsSuccessful = false;
        }
        $stmt2->close();

        // insert into UnqualifiedDog_Train_R3 
        $stmt3 = $conn->prepare("INSERT INTO UnqualifiedDog_Train_R3 (MicrochipNumber, StaffID) VALUES (?, ?)");
        $stmt3->bind_param("ii", $microchipNumber, $staffID);
        if ($stmt3->execute()) {
            echo "Training staff Inserted successfully<br>";
        } else {
            echo "Error inserting into UnqualifiedDog_Train_R3: " . $stmt3->error;
            $allInsertsSuccessful = false;
        }
        $stmt3->close();

        // before inserting into R4, check for StartDate uniqueness
        $checkStmt = $conn->prepare("SELECT StartDate FROM UnqualifiedDog_Train_R4 WHERE StartDate = ?");
        $checkStmt->bind_param("s", $startDate);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($result->num_rows > 0) {
            // startDate already exists
            header('Location: add_service_dog.php?error=Need to choose another training start date, the date you choose is fully booked.');
            exit;
        }
        $checkStmt->close();
        // insert into UnqualifiedDog_Train_R4 
        $stmt4 = $conn->prepare("INSERT INTO UnqualifiedDog_Train_R4 (StartDate, DaysRemaining) VALUES (?, ?)");
        $stmt4->bind_param("si", $startDate, $daysRemaining);
        if ($stmt4->execute()) {
            echo "Training days remaining Inserted successfully<br>";
        } else {
            echo "Error inserting into UnqualifiedDog_Train_R4: " . $stmt4->error;
            $allInsertsSuccessful = false;
        }
        $stmt4->close();

        if (!$allInsertsSuccessful) {
            header('Location: add_service_dog.php?error=Error adding unqualified service dog');
            exit;
        } else {
            $_SESSION['success_messages'][] = "New unqualified service dog with microchip#: " . $microchipNumber . " added successfully for training.";
            // header('Location: add_service_dog.php?success=New unqualified service dog added successfully for training');
            // exit;
        }
        header('Location: add_service_dog.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service Dog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <div class="container mt-3">
        <h3 class="text-center mb-4">Complete the form below to add a New Service Dog</h3>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_messages'])): ?>
    <?php foreach ($_SESSION['success_messages'] as $message): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['success_messages']);?>
<?php endif; ?>


<form action="" method="post">
<!-- Form fields for MicrochipNumber, ... (animal attributes) etc. -->
<div class="mb-3">
    <label for="MicrochipNumber" class="form-label">Microchip Number:</label>
    <input type="number" class="form-control" id="MicrochipNumber" name="MicrochipNumber" placeholder="Enter up to 4 digit, e.g., 5026" required>
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
    <label for="BirthDate" class="form-label">Birth Date:</label>
    <input type="date" class="form-control" id="BirthDate" name="BirthDate" required>
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

<!-- Service Certification Dropdown -->

<div class="mb-3">
    <label for="ServiceCertification" class="form-label">Service Certification:</label>
    <select class="form-select" id="ServiceCertification" name="ServiceCertification" onchange="toggleCertificationFields()" required>
        <option value="">Select Certification Status</option>
        <option value="Yes">Certified</option>
        <option value="No">Not Certified</option>
    </select>
</div>

<!-- Certification Number (shown if certified) -->
<div class="mb-3" id="CertifiedFields" style="display: none;">

<div class="mb-3">
    <label for="CertificationNumber" class="form-label">Certification Number:</label>
    <input type="text" class="form-control" id="CertificationNumber" name="CertificationNumber" placeholder="e.g., Cert-A2">
</div>

<!-- Qualification Date and Skill Set (shown if certified) -->
<div class="mb-3">
    <label for="QualificationDate" class="form-label">Qualification Date: </label>
    <input type="date" class="form-control" id="QualificationDate" name="QualificationDate">
</div>
<div class="mb-3">
    <label for="SkillSet" class="form-label">Skill Set: </label>
    <input type="text" class="form-control" id="SkillSet" name="SkillSet">
</div>

</div>

<!-- Training Status, StaffID, Start Date, Days Remaining (shown if not certified) -->
<div class="mb-3" id="UnqualifiedFields" style="display: none;">
<div class="mb-3">
    <label for="TrainingStatus" class="form-label">Training Status: </label>
    <input type="text" class="form-control" id="TrainingStatus" name="TrainingStatus">
</div>
<div class="mb-3">
    <label for="StaffID" class="form-label">Triaing Staff:</label>
    <select class="form-select" id="StaffID" name="StaffID">
        <option value="">Select Staff Member</option>
        <?php foreach ($staffMembers as $staff): ?>
            <option value="<?php echo htmlspecialchars($staff['StaffID']); ?>">
                <?php echo htmlspecialchars($staff['Name']); // Display the staff's name ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="mb-3">
    <label for="StartDate" class="form-label">Training Start Date: </label>
    <input type="date" class="form-control" id="StartDate" name="StartDate">
</div>
<div class="mb-3">
    <label for="DaysRemaining" class="form-label">Training Days Remaining: </label>
    <input type="number" class="form-control" id="DaysRemaining" name="DaysRemaining">
</div>
</div>
        
<div>
    <button type="submit" class="btn btn-success" name="submit">Save</button>
    <a href="animal_board.php" class="btn btn-danger">cancel</a>
    <a href="animal_board.php" class="btn btn-primary">Back to Animal Board</a>
</div>

</form>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMneT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
function toggleCertificationFields() {
    var certificationStatus = document.getElementById('ServiceCertification').value;
    var certifiedFields = document.getElementById('CertifiedFields');
    var unqualifiedFields = document.getElementById('UnqualifiedFields');

    if (certificationStatus === 'Yes') {
        certifiedFields.style.display = 'block';
        unqualifiedFields.style.display = 'none';
    } else if (certificationStatus === 'No') {
        certifiedFields.style.display = 'none';
        unqualifiedFields.style.display = 'block';
    } else {
        certifiedFields.style.display = 'none';
        unqualifiedFields.style.display = 'none';
    }
}
</script>

</body>
</html>
