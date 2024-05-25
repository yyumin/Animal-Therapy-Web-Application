<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Service Animal Management System </title>
  <!-- <style>
    h1 {text-align: center; margin-top: 10cm;}
    form {text-align: center;}
  </style> -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f5f5f5;
    }
    .login-container {
      padding: 2rem;
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 1rem;
    }
  </style>
  
</head>
<body>
  <!-- <h1>Service Animal Management System</h1>
  <form method="post" action="login.php">
    Username: <input type="text" name="username"> <br> <br>
    Password: <input type="password" name="password"> <br> <br>
    <input type="submit" value="Login">
  </form> -->
  <div class="login-container">
    <h1 class="text-center">Service Animal Management System</h1>
    <form method="post" action="login.php" class="mt-4">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" class="form-control" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <input type="submit" class="btn btn-primary w-100" value="Login">
    </form>
  </div>

  <?php
    include "footer.php";

    if (isset ($_POST["username"]) && isset ($_POST["password"])) {
      // Some trivial login creditals
      if ($_POST["username"] == "admin" && $_POST["password"] == "admin") {
        // echo "<script> location.href='patient_board.php'; </script>";
        // Redirect to the main_page.php instead of patient_board.php
        echo "<script> location.href='main_page.php'; </script>";
        exit();
      } else {
        showMessage("incorrect username or password", "error") ;
      }
    }
  ?>
</body>
</html>