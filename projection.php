<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projection and Selection</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <style>
    body {
      position: relative;
      left: 20px;
      top: 20px;
    }

    div {
      background-color: white;
      /* Main background color */
      color: grey;
      /* Text color */
      border-radius: 10px;
      /* Rounded corners */
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      /* Subtle shadow for depth */
      padding: 20px;
      /* Spacing inside the div */
      margin: 20px;
      /* Spacing outside the div */
      font-family: 'Arial', sans-serif;
      /* Modern, clean font */
      font-size: 16px;
      /* Readable text size */
      line-height: 1.6;
      /* Comfortable line spacing */
    }
  </style>
</head>

<body>
  <!-- <div>
    <button class="btn btn-success my-3">
      <a href="main_page.php" class="text-light" style="text-decoration: none;">Back to Main</a>
    </button>
  </div> -->

  <button class="btn btn-primary my-3">
    <a href="main_page.php" class="text-light" style="text-decoration: none;">Back to Main Page</a>
  </button>

  <h1>Projection and Selection</h1>

  <hr>

  <p>
    This page allows you to perform projection and selection in any tables, to search for the desired data.
  </p>

  <?php
  include "db_conn.php";

  $all_tables = $conn->query("SHOW TABLES");

  $tables = isset($_POST["tables"]) ? $_POST["tables"] : "";

  if ($tables != "") {
    $result = isset($_POST["tables"]) ? $conn->query("SELECT * FROM " . $tables) : null;
    $keys = isset($_POST["tables"]) ? array_keys($result->fetch_assoc()) : null;
    $keys_selected = [];
  }

  ?>


  <h3> Projection </h3>
  <div>
    <p>To perform projection, please choose a table from below, and select the columns you wish to project. </p>

    <form action="projection.php" method="POST">
      <label for="tables"> <b>Choose a Table:</b> </label>
      <select name="tables" id="tables" onchange="this.form.submit()">
        <option hidden selected> Choose from below: </option>
        <?php
        while ($table = mysqli_fetch_array($all_tables)) {
          echo ("<option value=" . $table[0] . ">" . $table[0] . "</option>");
        }
        ?>
      </select>
    </form>


    <form action="projection.php" method='POST'>
      <?php
      echo "<input name='tables' value='" . $tables . "' hidden>";
      if ($tables != "") {
        foreach ($keys as $key) {
          if (isset($_POST[$key])) {
            if ($_POST[$key] == '0') {
              echo "<input type='checkbox' name='0' value='1' onchange='this.form.submit()'> ";
            } else {
              echo "<input type='checkbox' name='" . $key . "' value='1' checked onchange='this.form.submit()'>";
              array_push($keys_selected, $key);
            }
          } else {
            echo "<input type='checkbox' name='" . $key . "' value='1' onchange='this.form.submit()'>";
          }
          echo "<label for='" . $key . "'> Select " . $key . "</label><br>";
        }
      }
      ?>
  </div>

  <br>

  <h3> Selection </h3>
  <div>
    <p>
      To perform selection, you will first need to set the total number of AND / OR operators to be used.
    </p>

    <input type="submit" value='Add Operator(s)'>

    <?php
    $opc = 0;
    if (isset($_POST["opc"])) {
      $opc = $_POST["opc"];
    }
    echo "<input type='number' min='0' name='opc' value='" . $opc . "'> <br> <br>";
    ?>

    <p>
      Next, choose the attribute and place the condition you want to search for.
    </p>

    <?php
    echo "<b>Select Operation:</b> &nbsp;";

    for ($x = 0; $x < $opc; $x++) {
      echo "<select name='select" . $x . "' id='select" . $x . "'>";
      $is_set = isset($_POST['select' . $x]);
      $val = $is_set ? $_POST['select' . $x] : null;
      echo ("<option hidden> Choose attribute: </option>");
      foreach ($keys_selected as $key) {
        echo ("<option value=" . $key . ">" . $key . "</option>");
      }
      echo "</select>";

      echo " = ";
      echo "<input type='text' name='field" . $x . "'>";

      echo "&nbsp; &nbsp; <select name='op" . $x . "' id='op" . $x . "'>";
      echo "<option hidden selected> Choose operator: </option>
          <option value='AND'> AND </option>
          <option value='OR'> OR </option>
          </select> &nbsp; &nbsp;";
    }

    echo "<select name='select" . $x . "' id='select" . $x . "'>";
    echo ("<option selected hidden> Choose attribute: </option>");
    foreach ($keys_selected as $key) {
      echo ("<option value=" . $key . ">" . $key . "</option>");
    }
    echo "</select>";

    echo " = ";
    echo "<input type='text' name='field" . $opc . "'>";
    ?>

    <?php
    $op = "";

    for ($x = 0; $x <= $opc; $x++) {
      if (isset($_POST['select' . $x]) && isset($_POST['field' . $x])) {
        if ($_POST['select' . $x] != 'Choose attribute:' && $_POST['field' . $x] != '') {
          if ($x == 0) {
            $op = $op . $_POST['select' . $x] . "='" . $_POST['field' . $x] . "'";
          } else {
            if ($_POST['op' . ($x - 1)] != 'Choose operator:') {
              $op = $op . " " . $_POST['op' . ($x - 1)] . " " . $_POST['select' . $x] . "='" . $_POST['field' . $x] . "'";
            } else {
              echo "<p style='color:red;'>Some field is left blank. No selection operation performed.</p>";
              $op = "";
              break;
            }
          }
        } else {
          echo "<p style='color:red;'>Some field is left blank. No selection operation performed.</p>";
          $op = "";
          break;
        }
      }
    }
    if ($op != "") {
      $op = " WHERE " . $op;
    }

    ?>
    <input type="submit" value="Submit">
  </div>

  </form>


  <?php echo "<h2>" . $tables . "</h2>";
  if ($tables != "") {

    $keys_query = "";
    foreach ($keys_selected as $key) {
      $keys_query = $keys_query . $key . ", ";
    }
    $keys_query = rtrim($keys_query, ", ");

    if ($keys_query == "")
      $keys_query = "*";


    echo $keys_query == "*" || count($keys_selected) == count($keys) ?
      "" :
      "<p style='color:green;'> Projected attributes: " . $keys_query . "</p>";

    $error = false;

    try {
      if ($op == "") {
        // projection
        $result = $conn->query("SELECT " . $keys_query . " FROM " . $tables);
      } else if ($keys_query == "" || $keys_query == "*"){
        // selection
        $result = $conn->query("SELECT * FROM " . $tables . $op);
      } else {
        // projection + selection
        $result = $conn->query("SELECT " . $keys_query . " FROM " . $tables .$op);
      }
      
    } catch (mysqli_sql_exception $e) {
      $error = true;
      echo "<p style='color:red;'> Data type unmatched. Please check if your conditions match attribute type. </p>";
    }
    if (!$error && $op != "") {
      echo "<p style='color:green;'>  Selection condition: " . substr($op, 6) . "</p>";
    }
    ?>

    <table class="table table-striped table-hover">
      <?php
      if ($result->num_rows > 0 && !$error) {

        echo "<tr>";
        foreach ($keys_selected as $key) {
          echo "<th>" . $key . "</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          foreach ($row as $key => $val) {
            if (in_array($key, $keys_selected)) {
              echo "<th>" . $val . "</th>";
            }
          }
          echo "</tr>";
        }
      } else {
        echo "<br> 0 results";
      }
  }
  ?>
  </table>

</body>

</html>