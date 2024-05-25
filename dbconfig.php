<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Configuration</title>
</head>
<body>
  <h1>Database Configuration</h1>
  
  <form method="post" action="dbconfig.php">
    <input type="submit" name="reconn" value="Reconnect"> &emsp;
    <input type="submit" name="disconn" value="Disconnect"> &emsp;
    <input type="submit" name="return" value="Return to Dashboard">
  </form> <br> <br>

  <?php
    include_once("footer.php");
  ?>

  <?php

    require_once("dblink.php");
    if (isset($_POST["reconn"])) { connectToDB(); } 
    if (isset($_POST["disconn"])) { disconnectFromDB(); } 
    if (isset($_POST["return"])) { 
      echo "<script> location.href='patient_board.php'; </script>";
      exit();
    } 
  ?>


  
</body>
</html>