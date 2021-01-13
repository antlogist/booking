<?php 

include_once "includes/dbh.include.php";

  if(isset($_GET['date'])) {
    $date = $_GET['date'];
  }

  if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mysqli = $conn;
    $stmt = $mysqli->prepare("INSERT INTO bookings (name, email, date) VALUES (?,?,?)");
    $stmt->bind_param('sss', $name, $email, $date);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>Booking Successful!</div>";
    $stmt->close();
    $mysqli->close();
  }

?>


<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

  <title>Booking System</title>
</head>

<body>
  <div class="container">
    <h1 class="text-center">Book for date: <?php echo date("m/d/Y", strtotime($date)); ?></h1>
    <div class="row">
      <div class="col-md-6 offset-md-3 my-5">
       <?php echo isset($msg) ? $msg : ""; ?>
        <form action="" method="post">
          <div class="mb-3">
            <label for="inputEmail" class="form-label">Email address</label>
            <input name="email" type="email" class="form-control" id="inputEmail">
          </div>
          <div class="mb-3">
            <label for="inputName" class="form-label">Name</label>
            <input name="name" type="text" class="form-control" id="inputName">
          </div>
          <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>

</html>
