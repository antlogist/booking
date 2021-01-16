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

  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

  <title>Hello, world!</title>
</head>

<body>
  <style>
    #shippingReport table {
      font-size: 14px;
    }

    #shippingReport table tr td {
      vertical-align: middle;
    }

    .table-hover tbody tr:hover td,
    .table-hover tbody tr:hover th {
      background-color: #9fc51c;
    }

  </style>
  <div class="container my-5">
    <div id="shippingReport"></div>
  </div>

  <!-- jQuery and Bootstrap Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>



  <script>
    (function report() {

      const data = <?php echo build_report(); ?>;
      let currentPageNumber = 1;
      const itemsPerPage = 4;
      const totalNumbersOfPages = Math.ceil(Number(data.length) / itemsPerPage);

      const shippingReport = document.getElementById("shippingReport");
      shippingReport.addEventListener("click", reportClick);

      reportRender(data.slice(0, itemsPerPage));


      function reportRender(data) {

        shippingReport.innerHTML = "";

        const reportData = data;

        if (!reportData.length) {
          return;
        }



        // DOM fragment element
        const fragment = document.createDocumentFragment();

        // Pagination buttons

        const paginationWrapper = document.createElement("div");
        paginationWrapper.classList.add("text-center", "my-3");

        const buttonPrev = document.createElement("button");
        buttonPrev.id = "btnPrev";
        buttonPrev.classList.add("btn", "btn-sm", "mr-1", "btn-prev");
        buttonPrev.textContent = "Prev";
        const buttonNext = document.createElement("button");
        buttonNext.id = "btnNext";
        buttonNext.classList.add("btn", "btn-sm", "ml-1", "btn-next");
        buttonNext.textContent = "Next";

        const iconPrev = document.createElement("i");
        iconPrev.classList.add("fa", "fa-angle-double-left", "mr-1");
        buttonPrev.insertAdjacentElement("afterbegin", iconPrev);

        const iconNext = document.createElement("i");
        iconNext.classList.add("fa", "fa-angle-double-right", "ml-1");
        buttonNext.insertAdjacentElement("beforeend", iconNext);

        paginationWrapper.appendChild(buttonPrev);
        paginationWrapper.appendChild(buttonNext);

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

            td.textContent = arr.length === index + 1 ? "pending" : value;
            tr.appendChild(td);
          });
          tbody.appendChild(tr);
        })

        // Append fragment
        table.appendChild(thead);
        table.appendChild(tbody);
        fragment.appendChild(table);
        fragment.appendChild(paginationWrapper);
        document.getElementById("shippingReport").appendChild(fragment);
      }

      function reportClick(e) {
        const target = e.target;
        const buttonPrevEl = target.classList.contains("btn-prev") || target.parentElement.classList.contains("btn-prev");
        const buttonNextEl = target.classList.contains("btn-next") || target.parentElement.classList.contains("btn-next");

        if (buttonPrevEl) {
          changePage("prev");
        }

        if (buttonNextEl) {
          changePage("next");
        }
      }

      const buttonPrev = document.getElementById("btnPrev");
      const buttonNext = document.getElementById("btnNext");

      function changePage(val) {
        if (val === "next") {
          if (currentPageNumber === totalNumbersOfPages) {
            return;
          }
          currentPageNumber++;
          reportRender(data.slice((currentPageNumber - 1) * itemsPerPage, ((currentPageNumber - 1) * itemsPerPage) + itemsPerPage));
        }

        if (val === "prev") {
          if (currentPageNumber === 1) {
            return;
          }
          currentPageNumber--;
          if (currentPageNumber === 1) {
            reportRender(data.slice(currentPageNumber - 1, itemsPerPage));
          } else {
            reportRender(data.slice((currentPageNumber - 1) * itemsPerPage, ((currentPageNumber - 1) * itemsPerPage) + itemsPerPage));
          }
        }
      }

    })();

  </script>

</body>

</html>
