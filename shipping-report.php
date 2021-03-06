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
  <div class="spinner-border-wrapper d-none" id="spinner">
    <div class="spinner-border text-light" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <style>
    .spinner-border-wrapper {
      position: fixed;
      display: -webkit-flex;
      display: -moz-flex;
      display: -ms-flex;
      display: -o-flex;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      left: 0;
      top: 0;
      z-index: 1000;
      background-color: rgba(0, 0, 0, 0.15);
    }

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

    table tbody tr {
      cursor: pointer;
    }
    
    table tbody tr.done {
      background-color: #efffb8;
    }

    .page-number {
      width: 30px;
      height: 30px;
      display: inline-block;
      background-color: darkred;
      border-radius: 50%;
      line-height: 30px;
      color: #FFF;
      text-shadow: 1px 1px 1px grey;
      border: 1px solid black;
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
      
      data.sort((a, b) => (a.date > b.date) ? 1 : (a.date === b.date) ? (new Date(a.timeslot) > new Date(b.timeslot) ? 1 : -1) : -1 );
      
      let currentPageNumber = 1;
      const itemsPerPage = 5;
      let totalNumbersOfPages = "";

      function totalPagesCount() {
        totalNumbersOfPages = Math.ceil(Number(data.length) / itemsPerPage);
      }

      totalPagesCount();

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
        const pageNumber = document.createElement("span");
        pageNumber.classList.add("text-center", "page-number");
        pageNumber.id = "pageNumber";
        pageNumber.textContent = currentPageNumber;


        const iconPrev = document.createElement("i");
        iconPrev.classList.add("fa", "fa-angle-double-left", "mr-1");
        buttonPrev.insertAdjacentElement("afterbegin", iconPrev);

        const iconNext = document.createElement("i");
        iconNext.classList.add("fa", "fa-angle-double-right", "ml-1");
        buttonNext.insertAdjacentElement("beforeend", iconNext);

        paginationWrapper.appendChild(buttonPrev);
        paginationWrapper.appendChild(pageNumber);
        paginationWrapper.appendChild(buttonNext);

        // Table
        const table = document.createElement("table");
        table.classList.add("table", "table-bordered", "table-hover", "table-sm", "table-responsive-md");

        // Head
        const thead = document.createElement("thead");
        //      thead.classList.add("thead-dark");
        const theadTr = document.createElement("tr");
        thead.appendChild(theadTr);

        // Th
        const thArr = Object.keys(reportData[0]);
        thArr.map((item, index) => {
          const th = document.createElement("th");
          th.setAttribute("scope", "col");
          th.textContent = item;
          if (index === 1) {
            th.style.minWidth = "100px";
          }
          theadTr.appendChild(th);
        })

        // Body
        const tbody = document.createElement("tbody");
        reportData.map((item) => {
          const tr = document.createElement("tr");
          const values = Object.values(item);
          values.map((value, index, arr) => {
            const td = document.createElement("td");

            td.textContent = (arr.length === index + 1 & (value === null || value === "")) ? "pending" : value;

            if (arr.length === index + 1) {
              td.id = "status" + item.id;
            }

            tr.appendChild(td);
          });
          // Status buttons
          const tdButtons = document.createElement("td");
          tdButtons.classList.add("text-center");
          tdButtons.style.minWidth = "100px";
          const deleteButton = document.createElement("button");
          deleteButton.classList.add("btn", "btn-sm", "btn-danger", "mx-1", "btn-delete");
          deleteButton.id = "deleteButton" + values[0];
          deleteButton.dataset.id = values[0];
          const deleteIcon = document.createElement("i");
          deleteIcon.dataset.id = values[0];
          deleteIcon.classList.add("fa", "fa-trash");
          deleteButton.appendChild(deleteIcon);

          const statusButton = document.createElement("button");
          statusButton.classList.add("btn", "btn-sm", "mx-1", "btn-status");
          if (item.status === "done") {
            statusButton.classList.add("status-done", "btn-danger");
            tr.classList.add("done");
          } else {
            statusButton.classList.remove("status-done", "btn-danger");
            tr.classList.remove("done");
          }
          statusButton.id = "statusButton" + values[0];
          statusButton.dataset.id = values[0];
          const statusIcon = document.createElement("i");
          statusIcon.dataset.id = values[0];
          statusIcon.classList.add("fa", "fa-check");
          statusButton.appendChild(statusIcon);

          tdButtons.appendChild(deleteButton);
          tdButtons.appendChild(statusButton);
          tr.appendChild(tdButtons);

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
        const buttonDeleteEl = target.classList.contains("btn-delete") || target.parentElement.classList.contains("btn-delete");
        const buttonStatusEl = target.classList.contains("btn-status") || target.parentElement.classList.contains("btn-status");

        if (buttonPrevEl) {
          changePage("prev");
        }

        if (buttonNextEl) {
          changePage("next");
        }

        if (buttonDeleteEl) {
          const id = target.dataset.id;
          const indexArr = data.findIndex(item => item.id === id);
          deleteRow(id, indexArr);
        }

        if (buttonStatusEl) {
          const id = target.dataset.id;
          const indexArr = data.findIndex(item => item.id === id);
          const button = document.getElementById("statusButton" + id);
          if (data[indexArr]["status"] === "pending") {
            changeStatus(id, indexArr, "done");
          } else {
            changeStatus(id, indexArr, "pending");
          }

        }
      }

      async function changeStatus(id, indexArr, status) {
        spinnerShow(true);
        await fetch('/booking/shipping-status.php?id=' + id + '&status=' + status, {
          method: 'POST',
        }).then(response => {
          data[indexArr]["status"] = status;
          changePage("current");
          console.log("Success");
          spinnerShow(false);
          return response;
        }).catch(err => console.log(err));
      }

      async function deleteRow(id, indexArr) {
        if (confirm('Are you sure you want to delete the row id ' + id + '?')) {
          spinnerShow(true);
          await fetch('/booking/shipping-delete.php?id=' + id, {
            method: 'POST',
          }).then(response => {
            data.splice(indexArr, 1);
            totalPagesCount();
            if (totalNumbersOfPages < currentPageNumber) {
              currentPageNumber = totalNumbersOfPages;
            }
            changePage("current");
            console.log("Success");
            spinnerShow(false);
            return response;
          }).catch(err => console.log(err));
        } else {
          return;
        }
      }

      const buttonPrev = document.getElementById("btnPrev");
      const buttonNext = document.getElementById("btnNext");

      function changePage(val) {
        spinnerShow(true);
        if (val === "next") {
          if (currentPageNumber === totalNumbersOfPages) {
            spinnerShow(false);
            return;
          }
          currentPageNumber++;
          reportRender(data.slice((currentPageNumber - 1) * itemsPerPage, ((currentPageNumber - 1) * itemsPerPage) + itemsPerPage));
        }

        if (val === "prev") {
          if (currentPageNumber === 1) {
            spinnerShow(false);
            return;
          }
          currentPageNumber--;
          if (currentPageNumber === 1) {
            reportRender(data.slice(currentPageNumber - 1, itemsPerPage));
          } else {
            reportRender(data.slice((currentPageNumber - 1) * itemsPerPage, ((currentPageNumber - 1) * itemsPerPage) + itemsPerPage));
          }
        }

        if (val === "current") {
          if (currentPageNumber === 1) {
            reportRender(data.slice(0, itemsPerPage));
          } else {
            reportRender(data.slice((currentPageNumber - 1) * itemsPerPage, ((currentPageNumber - 1) * itemsPerPage) + itemsPerPage));
          }
        }
        spinnerShow(false);
      }

      function spinnerShow(bool) {
        const spinner = document.getElementById("spinner");
        if (bool) {
          spinner.classList.remove("d-none");
        } else {
          spinner.classList.add("d-none");
        }
      }

    })();

  </script>
</body>

</html>
