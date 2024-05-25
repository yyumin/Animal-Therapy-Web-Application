<form method="post" action="tabs.php">
    <input type="submit" name="pinfo" value="Patient"> &emsp;
    <input type="submit" name="sinfo" value="Staff"> &emsp;
    <input type="submit" name="ainfo" value="Animal"> &emsp;
    <input type="submit" name="pands" value="Projection and Selection">
  </form> <br> <br>

<?php
    if (isset($_POST["pinfo"])) { echo "<script> location.href='patient_board.php'; </script>"; } 
    if (isset($_POST["sinfo"])) { echo "<script> location.href='staff_board.php'; </script>"; } 
    if (isset($_POST["ainfo"])) { echo "<script> location.href='animal_board.php'; </script>"; } 
    if (isset($_POST["pands"])) { echo "<script> location.href='projection.php'; </script>"; } 
  ?>