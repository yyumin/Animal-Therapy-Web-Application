<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TherapyTail Trackers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <style>
    /* body {
      padding-top: 40px;
      background: url('path_to_your_background_image.jpg') no-repeat center center fixed;
      background-size: cover;
    } */
    .container {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 15px;
      padding: 20px;
    }

    .centered-form {
      display: flex;
      justify-content: center;
      flex-direction: column;
      align-items: center;
    }

    .centered-form .btn {
      width: 200px;
      /* Set the width of the buttons */
      margin-bottom: 10px;
    }

    .welcome-message {
      text-align: center;
      margin-bottom: 30px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="welcome-message">
      <h1>Welcome to TherapyTail Trackers</h1>
      <p>Your centralized platform for managing disability support and animal therapy.</p>
    </div>

    <!-- Centered Tabs Navigation -->
    <div class="centered-form">
      <form method="post" action="tabs.php">
        <input type="submit" class="btn btn-primary" name="pinfo" value="Patient">
        <input type="submit" class="btn btn-warning" name="sinfo" value="Staff">
        <input type="submit" class="btn btn-success" name="ainfo" value="Animal">
        <input type="submit" class="btn btn-primary" name="pands" value="Projection & Selection">
      </form>
    </div>
  </div>
  <div style="text-align: center;">
    <img src="src/cover.gif" alt="" width="30%">
  
  </div>


  <!-- Bootstrap JS and Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMneT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
</body>

</html>