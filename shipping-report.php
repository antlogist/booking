<?php 

function build_report() {
  include_once "includes/dbh.include.php";
  $mysqli = $conn; 
  
  $sql = "SELECT id, name, phone, email, date, address, timeslot FROM bookings";
  $result = mysqli_query($mysqli, $sql);
  
  if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      echo "id: " . $row["id"]. " - Name: " . $row["name"] . "<br>";
    }
  } else {
    echo "0 results";
  }

  mysqli_close($mysqli);
  
}

build_report();

?>
