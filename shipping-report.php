<?php 

function build_report() {
  include_once "includes/dbh.include.php";
  $mysqli = $conn; 
  
  $sql = "SELECT id, date, timeslot, name, phone, email, address, status FROM bookings";
  $result = mysqli_query($mysqli, $sql);
  
  $rows = array();
  
  if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
  } else {
    echo "0 results";
  }

  mysqli_close($mysqli);
  
  $rows = json_encode($rows);
  
  return $rows;
  
}

?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <title>Hello, world!</title>
</head>

<body>
  <style>
/*
    table {
      width: 100%;
      table-layout: fixed;
    }

    table td {
      width: 100%;
    }
*/
    #shippingReport table {
      font-size: 14px;
    }
    #shippingReport table tr td {
      vertical-align: middle;
    }
    
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
      background-color: #9fc51c;
    }

  </style>
  <div class="container">
    <div id="shippingReport"></div>
  </div>

  <!-- jQuery and Bootstrap Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>



  <script>
    (function reportRender() {
      const reportData = <?php echo build_report(); ?>;

      if (!reportData.length) {
        return;
      }

      // DOM fragment element
      const fragment = document.createDocumentFragment();

      // Table
      const table = document.createElement("table");
      table.classList.add("table", "table-striped", "table-bordered", "table-hover", "table-sm", "table-responsive-md");

      // Head
      const thead = document.createElement("thead");
      //      thead.classList.add("thead-dark");
      const theadTr = document.createElement("tr");
      thead.appendChild(theadTr);

      // Th
      const thArr = Object.keys(reportData[0]);
      thArr.map((item) => {
        const th = document.createElement("th");
        th.setAttribute("scope", "col");
        th.textContent = item;
        theadTr.appendChild(th);
      })

      // Body
      const tbody = document.createElement("tbody");
      reportData.map((item) => {
        const tr = document.createElement("tr");
        const values = Object.values(item);
        values.map((value, index, arr) => {
          const td = document.createElement("td");
          
//          if (arr.length === index+1) {
//              console.log(arr.length, index);
//          } 
          
          td.textContent = arr.length === index+1 ? "pending" : value;
          tr.appendChild(td);
        });
        tbody.appendChild(tr);
      })

      // Append fragment
      table.appendChild(thead);
      table.appendChild(tbody);
      fragment.appendChild(table);
      document.getElementById("shippingReport").appendChild(fragment);
    })();
    
//    (function events() {
//      const el = document.getElementById("shippingReport");
//      el.addEventListener("mouseover", tableHover);
//      function tableHover(e) {
//        const target = e.target;
//        console.log(target);
//      }
//    })();

  </script>

</body>

</html>
