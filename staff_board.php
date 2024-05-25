
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>

<button class="btn btn-success mx-5 my-3">
      <a href="main_page.php" class="text-light" style="text-decoration: none;">Back to Main Page</a>
  </button>

  <div class="mx-5 my-5 border border-primary p-2 mb-2 border-opacity-75">
  <h2 >Staff Information</h2>
  <div >
    <a href="add_staff.php" class="btn btn-primary">Add Staff</a>
  </div>
  </container>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Staff ID</th>
        <th>Name</th>
        <th>Role</th>
      </tr>
    </thead>
    <tbody>
<?php
include "db_conn.php";
      $query = "SELECT * FROM Staff;";
      $result = $conn->query($query);
      if($result->num_rows>0)
      {while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["StaffID"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Role"]) . "</td>";
          echo "<td>";

          echo "<form method='post' action='delete_staff.php' onsubmit='return confirmDelete();'>";
          echo "<input type='hidden' name='StaffID' value='" . $row["StaffID"] . "'>";
          echo "<button type='submit' name='delete' class='btn btn-danger'>Delete</button>";
          echo "</form>";
          echo "</td>";
          echo "</tr>";
          echo "</td>";
          echo "</tr>";
        }
        
      }
      else {
        echo "0 results";
      }
      $conn->close();
?>
    </tbody>
  </table>
  </div>

  <div class="mt-5 mx-5 border border-primary p-2 mb-2 border-opacity-75">
  <h2>Therapy Session</h2>
  <article class="p-2 mb-2 text-body-secondary bg-body-secondary rounded">Each Session Type will be provided at most once at a single day.</article>
<!--   
  <div>
    <a href="add_therapy_session.php" class="btn btn-primary">Add Therapy Session</a>
  </div> -->

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Session Date</th>
        <th>Staff Name</th>
        <th>Animal</th>
        <th>Session Type</th>
        <th>Max Capacity</th>
      </tr>
    </thead>
    <tbody>
    <?php
    include "db_conn.php";
      $query = "SELECT c.SessionDate, c.StaffID, c.MicrochipNumber, ts1.SessionType, ts1.MaxCapacity FROM ConductBy c JOIN TherapySession_Assigned_R2 ts1 ON c.SessionDate = ts1.SessionDate GROUP BY c.SessionDate, c.MicrochipNumber;";
      $result = $conn->query($query);
      if($result->num_rows>0)
      {while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["SessionDate"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["StaffID"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["MicrochipNumber"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["SessionType"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["MaxCapacity"]) . "</td>";
          echo "<td>";

          echo "</td>";

          echo "</tr>";
        }
        
      }
      else {
        echo "0 results";
      }
      $conn->close();
?>
  </table>
    </div>

    <div class="mt-5 mx-5 border border-primary p-2 mb-2 border-opacity-75">
  <h2>Check How Many Therapy Animals Are In The Same Session By A Chosen Date</h2>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <div class="mb-3">
      <label for="sessionDate" class="form-label">Select a Date:</label>
      <input type="date" class="form-control" id="sessionDate" name="sessionDate" required>
    </div>
    <button type="submit" class="btn btn-primary">Check</button>
  </form>

  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      include "db_conn.php";
      
      $sessionDate = $_POST['sessionDate'];
      //Demo-ing the Query: Aggregation with GROUP BY
      $query = "SELECT COUNT(*) as AnimalCount, SessionType FROM TherapySession_Assigned_R2 WHERE SessionDate = ? GROUP BY SessionType";
      
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $sessionDate);
      $stmt->execute();
      $result = $stmt->get_result();

       if ($result->num_rows > 0) {
        echo "<div>Number of therapy animals on " . htmlspecialchars($sessionDate) . ":</div>";
        while ($row = $result->fetch_assoc()) {
            echo "<div>" . $row["SessionType"] . ": " . $row["AnimalCount"] . "</div>";
        }
    } else {
        echo "<div>No therapy sessions found on this date.</div>";
    }
      
      $stmt->close();
      $conn->close();
  }
  ?>
</div>


<div class="mt-5 mx-5 border border-primary p-2 mb-2 border-opacity-75">
<h2>Check available therapy animals:</h2>
<form action="staff_board.php" method="post"> 
 <select class="form-select" name="CertificationType" onchange="this.form.submit()">
  <option value="2" <?php echo ($selectedCertificationType == '2') ? 'selected' : ''; ?>>Not chosen</option>
  <option value="1" <?php echo ($selectedCertificationType == '1') ? 'selected' : ''; ?>>Have Certification</option>
  <option value="0" <?php echo ($selectedCertificationType == '0') ? 'selected' : ''; ?>>No Certification</option>
</select>
</form>

<table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Microchip Number</th>
        <th>Birth Date</th>
        <th>Health Status</th>
        <th>Species</th>
        <th>Age</th>
        <th>Temperament</th>
        <th>Origin ID</th>
        <th>Therapy Certification</th>
      </tr>
    </thead>
    <tbody>

<?php
include "db_conn.php";

      $selectedCertificationType = isset($_POST['CertificationType']) ? $_POST['CertificationType'] :'2';

      if($selectedCertificationType == '2'){
        echo "<div class=' alert alert-info text-center'>Waiting for input</div>";
        $query = "";
       } else {
      if ($selectedCertificationType == '1') {
        //Demo-ing the Query: Join
        $query = "SELECT a.*, t.TherapyCertification FROM Animal_From a JOIN TherapyAnimal t ON a.MicrochipNumber=t.MicrochipNumber WHERE t.therapycertification='1';";
      } elseif ($selectedCertificationType == '0') {
        $query = "SELECT a.*, t.TherapyCertification FROM Animal_From a JOIN TherapyAnimal t ON a.MicrochipNumber=t.MicrochipNumber WHERE t.therapycertification='0';";
      }
    }
      
    if (isset($query) && $query != "") {
      $result2 = $conn->query($query);
      if (!$result2) {
        echo "Error: " . $conn->error;
    } else {
      if($result2->num_rows>0){
        while($row = $result2->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["MicrochipNumber"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["BirthDate"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["HealthStatus"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Species"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Age"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Temperament"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OriginID"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["TherapyCertification"]) . "</td>";
          echo "<td>";
          echo "</tr>";
        } 
      }
      else {
        echo "0 results";
      }
    }
  }
      $conn->close();
?>
    </tbody>
</table>
</body>
</div>

  <div id="staffCheckSection" class="mx-5 mt-5 border border-primary p-2 mb-2 border-opacity-75">
  <h2>Find Staff who takes charge of all Session Types. </h2>
  <form action="<?php echo $_SERVER['PHP_SELF'];?>#staffCheckSection" method="post">
    <input type="hidden" name="checkStaff" value="yes">
    <button type="submit" class="btn btn-primary">Check</button>
  </form>


<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this staff member? This action cannot be undone.');
}
</script>

</body>
</html>

<?php
include "db_conn.php"; // Assume this file contains your database connection setup

$runQuery = false;

if(isset($_POST['checkStaff']) && $_POST['checkStaff'] == 'yes') {
  $runQuery = true;
}

//Demo-ing the Query: Division
if ($runQuery) {
$query = "SELECT s.StaffID, s.Name
          FROM Staff s
          WHERE NOT EXISTS (
            SELECT t1.SessionType
            FROM TherapySession_Assigned_R1 t1
            WHERE NOT EXISTS (
              SELECT c.SessionDate
              FROM ConductBy c
              WHERE c.StaffID = s.StaffID AND c.SessionDate IN (
                SELECT ts2.SessionDate
                FROM TherapySession_Assigned_R2 ts2
                WHERE ts2.SessionType = t1.SessionType
              )
            )
          );";

$result = $conn->query($query);

if ($result) {
    echo "<table class='table table-striped table-hover'>";
    echo "<thead class='thead-dark'>
           <tr>
           <th>Staff ID</th>
           <th>Name</th>
           </tr>
           </thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row["StaffID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Name"]) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}
}
?>

</div>
<br>
<br>
<br>
