<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
  <h2>Patient Information</h2>
  <div>
    <button class="btn btn-success my-3">
      <a href="main_page.php" class="text-light" style="text-decoration: none;">Back to Main</a>
    </button>
  </div>
  <hr>

  <form action="patient_board.php" method="post">
    <div class="col">
      <select class="form-select" name="patientType" onchange="this.form.submit()">
        <option selected hidden> Choose here </option>
        <option value="all">All Patient</option>
        <option value="service">Therapy Patient</option>
        <option value="therapy">Disabled Person</option>
      </select>
    </div>
  </form>

  <!-- <div>
    <button class="btn btn-primary my-3">
      <a href="add_patient.php" class="text-light" style="text-decoration: none;">Add New Patient</a>
    </button>
  </div> -->

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Contact Number</th>
        <th>Age</th>
        <th>Therapy Reason / Medical Condition</th>
        <th>Paired Service Animal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      include("db_conn.php");

      $type = isset($_POST['patientType']) ? $_POST['patientType'] : 'all';

      if ($type == 'therapy' || $type == 'all') {
        $query = "SELECT * FROM TherapyPatient;";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ContactNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars(is_null($row["Age"]) ? "N/A" : $row["Age"]) . "</td>";
            echo "<td>" . htmlspecialchars(is_null($row["TherapyReason"]) ? "N/A" : $row["TherapyReason"]) . "</td>";
            echo "<td> N/A </td>";
            echo "</tr>";
          }
        } else {
          echo "0 results";
        }
      }


      if ($type == 'service' || $type == 'all') {
        $query = "SELECT * FROM DisabledPerson;";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ContactNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars(is_null($row["Age"]) ? "N/A" : $row["Age"]) . "</td>";
            echo "<td>" . htmlspecialchars(is_null($row["MedicalCondition"]) ? "N/A" : $row["MedicalCondition"]) . "</td>";
            echo "<td>" . htmlspecialchars(is_null($row["MicrochipNumber"]) ? "N/A" : $row["MicrochipNumber"]) . "</td>";
            echo "</tr>";
          }
        }
      }

      $conn->close();
      ?>
    </tbody>
  </table>

</body>

</html>