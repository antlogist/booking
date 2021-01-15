<?php 

include_once "includes/dbh.include.php";
$mysqli = $conn;

  if(isset($_GET['date'])) {
    $date = $_GET['date'];
    
    $stmt = $mysqli->prepare("select * from bookings where date = ?");
    $stmt->bind_param('s', $date);
    $bookings = array();
    
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['timeslot'];
            }
  
            $stmt->close();
        }
    }
  }

  if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslot = $_POST['timeslot'];
    
    $stmt = $mysqli->prepare("select * from bookings where date = ? AND timeslot = ?");
    $stmt->bind_param('ss', $date, $timeslot);
    
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
          $msg = "<div class='alert alert-danger'>Already Booked!</div>";
        } else {
          $stmt = $mysqli->prepare("INSERT INTO bookings (name, timeslot, email, date) VALUES (?,?,?,?)");
          $stmt->bind_param('ssss', $name, $timeslot, $email, $date);
          $stmt->execute();
          $msg = "<div class='alert alert-success'>Booking Successful!</div>";
          $bookings[] = $timeslot;
          $stmt->close();
          $mysqli->close();
        }
    }
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
  <div class="container" id="slotsContainer">
    <h1 class="text-center">Book for date: <?php echo date("m/d/Y", strtotime($date)); ?></h1>
    <div class="row">
      <?php echo isset($msg) ? $msg : ""; ?>
      
      <?php $timeslots = timeslots($duration, $cleanup, $start, $end); 
      foreach ($timeslots as $ts) { ?>

      <div class="col-md-2 mb-3">
       <?php 
        if (in_array($ts, $bookings)) { ?>
          <button 
            class="btn btn-danger">
              <?php echo $ts; ?>
          </button>
        <?php } else{ ?>
          <button 
            class="btn btn-success book" 
            data-bs-toggle="modal" 
            data-bs-target="#bookingModal" 
            data-timeslot="<?php echo $ts; ?>">
              <?php echo $ts; ?>
          </button>
        <?php } ?>
      </div>

      <?php  
      }
    ?>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Booking: <span id="slotTime"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label for="inputTime" class="form-label">Time</label>
              <input readonly name="timeslot" type="text" class="form-control" id="inputTime" required>
            </div>
            <div class="mb-3">
              <label for="inputEmail" class="form-label">Email address</label>
              <input name="email" type="email" class="form-control" id="inputEmail" required>
            </div>
            <div class="mb-3">
              <label for="inputName" class="form-label">Name</label>
              <input name="name" type="text" class="form-control" id="inputName" required>
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

  <script>
    const el = document.querySelector("#slotsContainer");
    el.addEventListener("click", slotsContainerClick)

    function slotsContainerClick(el) {
      const target = el.target;
      const slotButton = target.classList.contains("book");

      // If Slot button
      if (slotButton) {
        const val = target.dataset.timeslot;
        const modalTitle = document.querySelector("#slotTime");
        // Change modal window title
        modalTitle.innerHTML = val;
        // Change input time
        const inputTime = document.querySelector("#inputTime");
        inputTime.value = val;
      }
    }

  </script>

</body>

</html>
