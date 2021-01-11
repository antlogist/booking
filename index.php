<?php 

function build_calendar($month, $year) {
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
  $calendar.= "<center><h2>$monthName $year</h2></center>";
//  
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
    
    if ($datetoday == $date) {
      $calendar.= "<td class='table-active'><h4>$currentDay</h4></td>";
    } else {
      $calendar.= "<td><h4>$currentDay</h4></td>";
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
  
  echo $calendar;
  
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
    <h1>Booking System</h1>

    <?php 
      $dateComponents = getdate();
      $month = $dateComponents["mon"];
      $year = $dateComponents["year"];
   
      echo build_calendar($month, $year);
    ?>
 </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>

</html>
