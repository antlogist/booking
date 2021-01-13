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

$duration = 60;
$cleanup = 0;
$start = "08:00";
$end = "18:00";

function timeslots($duration, $cleanup, $start, $end) {
  $start = new DateTime($start);
  $end = new DateTime($end);
  $interval = new DateInterval("PT" . $duration . "M");
  $cleanupInterval = new DateInterval("PT" . $cleanup . "M");
  
  $slots = array();
  
  for($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
    $endPeriod = clone $intStart;
    $endPeriod->add($interval);
    if ($endPeriod > $end) {
      break;
    }
    
    $slots[] = $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
  }
  return $slots;
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
      <!--
      <div class="col-md-6 offset-md-3 my-5">
       <!--?php echo isset($msg) ? $msg : ""; ?>
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
-->
      <?php $timeslots = timeslots($duration, $cleanup, $start, $end); 
      foreach ($timeslots as $ts) { ?>

      <div class="col-md-2 mb-3">
        <button class="btn btn-success"><?php echo $ts; ?></button>
      </div>

      <?php  
      }
    ?>
    </div>
  </div>

  <!-- Button trigger modal -->
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
  </button>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Booking: <span id="slot"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php echo isset($msg) ? $msg : ""; ?>
        <form action="" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label for="inputEmail" class="form-label">Email address</label>
              <input name="email" type="email" class="form-control" id="inputEmail">
            </div>
            <div class="mb-3">
              <label for="inputName" class="form-label">Name</label>
              <input name="name" type="text" class="form-control" id="inputName">
            </div>

          </div>
          <div class="modal-footer">
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>

</html>
