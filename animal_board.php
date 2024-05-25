<?php
  include "db_conn.php";


  $selectedAnimalType = isset($_POST['animalType']) ? $_POST['animalType'] : 'all';
  $selectedSpecies = isset($_POST['speciesFilter']) && $_POST['speciesFilter'] != 'all' ? $_POST['speciesFilter'] : null;

  $speciesSql = "SELECT DISTINCT Species FROM Animal_From ORDER BY Species";
  $speciesResult = $conn->query($speciesSql);
  $speciesList = $speciesResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Animal Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body style="padding-left: 20px;">
  <h1>Animal Dashboard</h1>

<div>
  <button class="btn btn-primary my-3">
      <a href="add_service_dog.php" class="text-light" style="text-decoration: none;">Add Service Dog</a>
  </button>
  <button class="btn btn-primary my-3">
      <a href="add_therapy_animal.php" class="text-light" style="text-decoration: none;">Add Therapy Animal</a>
  </button>
  <button class="btn btn-primary my-3">
      <a href="add_retired_animal.php" class="text-light" style="text-decoration: none;">Add Retired Animal</a>
  </button>
  <button class="btn btn-success my-3">
      <a href="main_page.php" class="text-light" style="text-decoration: none;">Back to Main</a>
  </button>
</div>

<div class="mx-5 my-5 border border-primary p-2 mb-2 border-opacity-75">
  <h2>Animal Information</h2>

<form action="animal_board.php" method="post">
<div class="row">  

<div class="col">
<!-- <select class="form-select" name="animalType" onchange="this.form.submit()">
  <option selected hidden> Choose here </option>
  <option value="all">All Animals</option>
  <option value="service">Service Dogs</option>
  <option value="therapy">Therapy Animals</option>
  <option value="retired">Retired Animals</option>
</select> -->
<select class="form-select" name="animalType" onchange="this.form.submit()">
  <option value="all" <?php echo ($selectedAnimalType == 'all') ? 'selected' : ''; ?>>All Animals</option>
  <option value="service" <?php echo ($selectedAnimalType == 'service') ? 'selected' : ''; ?>>Service Dogs</option>
  <option value="therapy" <?php echo ($selectedAnimalType == 'therapy') ? 'selected' : ''; ?>>Therapy Animals</option>
  <option value="retired" <?php echo ($selectedAnimalType == 'retired') ? 'selected' : ''; ?>>Retired Animals</option>
</select>
</div>

<div class="col">
<select class="form-select" name="speciesFilter" onchange="this.form.submit()">
  <option value="all" <?php echo ($selectedSpecies == 'all') ? 'selected' : ''; ?>>All Species</option>
  <?php foreach ($speciesList as $speciesRow): ?>
    <option value="<?php echo htmlspecialchars($speciesRow['Species']); ?>" <?php echo ($selectedSpecies == $speciesRow['Species']) ? 'selected' : ''; ?>>
      <?php echo htmlspecialchars($speciesRow['Species']); ?>
    </option>
  <?php endforeach; ?>
</select>
</div>

</div>
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
      </tr>
    </thead>
    <tbody>

<?php
include "db_conn.php";
$query = "SELECT * FROM Animal_From"; // base query
      
// Filter by animal type
if ($selectedAnimalType != 'all') {
  if ($selectedAnimalType == 'service') {
      $query = "SELECT a.* FROM Animal_From a JOIN ServiceDog s ON a.MicrochipNumber = s.MicrochipNumber";
  } elseif ($selectedAnimalType == 'therapy') {
      $query = "SELECT a.* FROM Animal_From a JOIN TherapyAnimal t ON a.MicrochipNumber = t.MicrochipNumber";
  } elseif ($selectedAnimalType == 'retired') {
      $query = "SELECT a.* FROM Animal_From a JOIN RetiredAnimal_Adopt r ON a.MicrochipNumber = r.MicrochipNumber";
  }
}

// if a specific species is selected
if ($selectedSpecies) {
  // Check if WHERE clause is needed
  if (strpos($query, 'JOIN') !== false) {
      $query .= " AND";
  } else {
      $query .= " WHERE";
  }
  $query .= " Species = '" . $conn->real_escape_string($selectedSpecies) . "'";
}
//fetch data from db
$result = $conn->query($query);
if($result->num_rows>0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
          echo "<td>" . htmlspecialchars(intval($row["MicrochipNumber"])) . "</td>";
          echo "<td>" . htmlspecialchars($row["BirthDate"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["HealthStatus"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Species"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Age"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Temperament"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OriginID"]) . "</td>";

          echo "<td>";

          echo "<a href='update_animal.php?MicrochipNumber=" . intval($row["MicrochipNumber"]) . "' class='btn btn-info btn-sm'>Update</a> ";
          echo "<a href='delete_animal.php?MicrochipNumber=" . intval($row["MicrochipNumber"]) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>";

          echo "</td>";

          echo "</tr>";
        }
      } else {
        echo "0 results";
      }
      $conn->close();
  ?>
</tbody>
</table>
</div>

<hr>

<div class="mx-5 my-5 border border-primary p-2 mb-2 border-opacity-75">

<h2> Animal Data Analysis </h2>
<hr>
<div class="container my-4 p-3 border rounded">
<h3>Species Analysis by Age Threshold</h3>
<p>
Enter an age threshold to find species with an average age above this value. <br>
A table will display each species that meets the criteria, along with their average age and the count of animals above the specified age threshold.
</p>

<form action="animal_board.php" method="post">
  <div class="mb-3">
    <label for="ageThreshold" style="font-size: 1.3rem;">Age Threshold:</label>
        <input type="number" name="ageThreshold" min="0" 
        required placeholder="Enter a whole number" value="<?php echo isset($_POST['ageThreshold']) ? $_POST['ageThreshold'] : ''; ?>">
    <button type="submit" class="btn btn-success" name="findSpecies">Find Species</button>
  </div>
</form>



<?php 
include "db_conn.php";

if (isset($_POST['findSpecies'])) {
    $ageThreshold = $_POST['ageThreshold'];

    // counting animals multiple times - joint Animal_From with itself
    // WRONG !!!!
    // $query = "SELECT a.Species, AVG(a.Age) AS AvgAge, COUNT(b.MicrochipNumber) AS CountOfAnimals
    //           FROM Animal_From a
    //           JOIN Animal_From b ON a.Species = b.Species AND b.Age > ?
    //           GROUP BY a.Species
    //           HAVING AVG(a.Age) > ?";


// SQL Query: Nested Aggregation with GROUP BY
$query = "SELECT Species, AVG(Age) AS AvgAge, 
          (SELECT COUNT(*) FROM Animal_From WHERE Species = a.Species AND Age > ?) AS CountOfAnimalsAboveThreshold
          FROM Animal_From a
          GROUP BY Species
          HAVING AVG(Age) > ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $ageThreshold, $ageThreshold);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo "<table class=\"table table-bordered\">";
        echo "<tr>";
        echo "<th>Species</th>";
        echo "<th>Average Age</th>";
        echo "<th>Number of Animals Above Threshold</th>";
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Species']) ."</td>";
            echo "<td>" . htmlspecialchars(intval($row['AvgAge'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['CountOfAnimalsAboveThreshold']) ."</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No species found with an average age above the specified threshold.</p>";
    }

    $stmt->close();
}
?>
</div>

<div class="container my-4 p-3 border rounded">
<h3>Retirement Reasons Analysis</h3>
<p>
    Explore the reasons for the retirement of animals. <br>
    This analysis shows the number of animals retired for each reason, filtering to include only those reasons where the number of retired animals exceeds a specified threshold.
</p>
<form action="" method="get">
  <label for="minCount">Minimum Number of Animals:</label>
  <input type="number" id="minCount" name="minCount" required
    placeholder="Enter a whole number" value="<?php echo isset($_GET['minCount']) ? $_GET['minCount'] : ''; ?>">
    <button type="submit" class="btn btn-success">Show Data</button>
</form>

<?php 
include "db_conn.php"; 

// Check if minCount parameter is set and not empty
if (isset($_GET['minCount']) && $_GET['minCount'] != '') {
    $minCount = intval($_GET['minCount']);

// SQL Query: Aggregation with HAVING
    $query = "SELECT ReasonForRetirement, COUNT(*) AS NumberOfAnimals
              FROM RetiredAnimal_Adopt
              GROUP BY ReasonForRetirement
              HAVING COUNT(*) > ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $minCount);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result-> num_rows > 0) {
        echo "<table class=\"table table-bordered\">";
        echo "<tr><th>Reason for Retirement</th><th>Number of Animals</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ReasonForRetirement']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NumberOfAnimals']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No retirement reasons found with a count above the specified minimum.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Please enter a minimum number of animals to view the data.</p>";
}

?>
</div>
</div>



</body>
</html>



