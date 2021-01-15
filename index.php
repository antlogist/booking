<?php 

function build_calendar($month, $year) {
  
  include_once "includes/dbh.include.php";
  
  $mysqli = $conn;
//  $stmt = $mysqli->prepare("select * from bookings where MONTH(date) = ? AND YEAR(date) = ?");
//  $stmt->bind_param('ss', $month, $year);
//  $bookings = array();
//  
//  if($stmt->execute()){
//      $result = $stmt->get_result();
//      if($result->num_rows>0){
//          while($row = $result->fetch_assoc()){
//              $bookings[] = $row['date'];
//          }
//
//          $stmt->close();
//      }
//  }
  
  // Days of week
  $daysOfWeek = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
  // First day of the month. mktime() - Return the Unix timestamp for a date
  $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
  //Get number of days in a month
  $numberDays = date("t", $firstDayOfMonth);
  // Get info about the first day of the month
  $dateComponents = getdate($firstDayOfMonth);
  // Get name of the month
  $monthName = $dateComponents["month"];
  // Get the index of the first day
  $dayOfWeek = $dateComponents["wday"];
  // Get current date
  $datetoday = date("Y-m-d");

  // HTML table
  $calendar = "<table class='table table-bordered table-responsive'>";
  $calendar.= "<center class='mb-3'><h2>$monthName $year</h2><br>";
  
  $calendar.= "<a href='?month=" . date("m", mktime(0, 0, 0, $month-1, 1, $year)) . "&year=" .date("Y", mktime(0, 0, 0, $month-1, 1, $year)) . "' class='btn btn-primary'>Previous month</a>";
  
  $calendar.= "<a href='?month=" . date("m") . "&year=" .date("Y") . "' class='btn btn-primary'>Current month</a>";
  
  $calendar.= "<a href='?month=" . date("m", mktime(0, 0, 0, $month+1, 1, $year)) . "&year=" .date("Y", mktime(0, 0, 0, $month+1, 1, $year)) . "' class='btn btn-primary'>Next month</a></center>";
  
  $calendar.= "<thead>";
  $calendar.= "<tr>";
  
  // Calendar headers
  foreach ($daysOfWeek as $day) {
    $calendar.= "<th scope='col'>$day</th>";
  }
  
  $calendar.= "</tr>";
  $calendar.= "</thead>";
  
  // Only 7 columns on table
  if ($dayOfWeek > 0) {
    for ($k=0; $k<$dayOfWeek; $k++) {
      $calendar.= "<td width='14.2%'></td>";
    }
  }
  
  // Initiatin the day counter
  $currentDay = 1;
  
  //Get the month number
  $month = str_pad($month, 2, "0", STR_PAD_LEFT);
  
  while ($currentDay <= $numberDays) {
    
    // If Saturday reached start a new column
    
    if ($dayOfWeek == 7) {
      $dayOfWeek = 0;
      $calendar.= "<tr></tr>";
    }
    
    $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
    $date = "$year-$month-$currentDayRel";
    
    $today = $date == date("Y-m-d") ? "table-active" : "";
    
    if ($date < date("Y-m-d")) {
      $calendar.= "<td style='width: 14%;'><h4>$currentDay</h4><a href='#' class='btn btn-danger btn-sm'>N/A</a></td>";
    } else {
      $totalbookings = checkSlots($mysqli, $date);
      if ($totalbookings == 10) {
        $calendar.= "<td style='width: 14%;' class=" . $today ."><h4>$currentDay</h4><a href='#' class='btn btn-danger btn-sm'>All Booked</a></td>";
      } else {
        $calendar.= "<td style='width: 14%;' class=" . $today ."><h4>$currentDay</h4><a href='book.php?date=" . $date . "' class='btn btn-success btn-sm'>Book</a></td>"; 
      }
    }
    
    // Incrementing the counters
    $currentDay++;
    $dayOfWeek++;  
  }
  
  // Last week row in a month
  if ($dayOfWeek != 7) {
    $remainingDays = 7-$dayOfWeek;
    for ($i=0;$i<$remainingDays;$i++) {
      $calendar.= "<td></td>";
    }
  }
  
  $calendar.= "</tr>";
  $calendar.= "</table>";
  
  return $calendar;
  
}

function checkSlots($mysqli, $date) {
  $stmt = $mysqli->prepare("select * from bookings where date = ?");
  $stmt->bind_param('s', $date);
  $totalbookings = 0;
  
  if($stmt->execute()){
      $result = $stmt->get_result();
      if($result->num_rows>0){
          while($row = $result->fetch_assoc()){
              $totalbookings++;
          }

          $stmt->close();
      }
  }
  return $totalbookings;
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
    <h1 class="text-center">Booking System</h1>

    <?php 
      $dateComponents = getdate();
      if(isset($_GET['month']) && isset($_GET['year'])){
        $month = $_GET['month']; 			     
        $year = $_GET['year'];
      }else{
        $month = $dateComponents['mon']; 			     
        $year = $dateComponents['year'];
      }
      echo build_calendar($month,$year);
    ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>

</html>
