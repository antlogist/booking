<?php

  include_once "includes/dbh.include.php";
  $mysqli = $conn; 
  
  $id = $_GET['id'];
  
  $sql = "DELETE FROM bookings WHERE id = '$id'";


  if (mysqli_query($mysqli, $sql)) {
    echo "Success";
  } else {
    echo mysqli_error;
  }

mysqli_close($mysqli);
