<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once("globalhead.php"); ?>


    
    <link rel="stylesheet" href="fonts/icomoon/style.css">
  
    <link href='fullcalendar/packages/core/main.css' rel='stylesheet' />
    <link href='fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
    
    
    <!-- Bootstrap CSS -->

    
    <!-- Style -->

</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
  
<?php require_once("globalnav.php"); ?>
<button class="btn btn-primary float-end" onclick="searchByGroupId()">Szukaj</button>
<select class="form-control w-25 float-end" name="evens" id="groupIdInput">
  <option value="v630">v630</option>
  <option value="messer">messer</option>
</select>
    <br /><br />
<div style="width:85%;margin-left:auto;margin-right:auto;" id='calendar'></div>
<br />
<script src='dist/index.global.js'></script>
<script>
      var calendar;
  var eventsarray=[];

  function searchByGroupId() {
      var groupIdInput = document.getElementById('groupIdInput').value;
      var filteredEvents = eventsarray.filter(function(event) {
        return event.groupId === groupIdInput;
      });
      calendar.removeAllEvents();
      calendar.addEventSource(filteredEvents);
    }
  </script>
<?php
          require_once('dbconnect.php');
          $sqlv="SELECT p.[ProjectName],
          CAST(p.ModificationDate AS DATE) AS ModificationDate
   FROM dbo.Product_V630 p
   GROUP BY p.[ProjectName], CAST(p.ModificationDate AS DATE);";
          $datas1 = sqlsrv_query($conn, $sqlv);
          echo "<script>";
          while ($row = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
            echo 'eventsarray.push({
              "groupId": "v630",
              "title": "'.$row["ProjectName"].'",
              "start": "'.date_format($row["ModificationDate"], 'Y-m-d').'",
              "color": "#227525"
            });';
          }
        $sqlm="SELECT p.[WoNumber],
        CAST(p.ArcDateTime AS DATE) AS ModificationDate
 FROM dbo.PartArchive_Messer p
 GROUP BY p.[WoNumber], CAST(p.ArcDateTime AS DATE);";
         $datas2 = sqlsrv_query($conn, $sqlm);
         while ($row = sqlsrv_fetch_array($datas2, SQLSRV_FETCH_ASSOC)) {
        
          echo 'eventsarray.push({
            "groupId": "messer",
            "title": "'.$row["WoNumber"].'",
            "start": "'.date_format($row["ModificationDate"], 'Y-m-d').'",
            "color": "#1b1b63"
          });';
            
        } 
        echo "</script>";
        ?>
<script>

const currentDate = new Date();
const year = currentDate.getFullYear();
const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Dodanie zera z przodu, jeśli miesiąc jest jednocyfrowy
const day = String(currentDate.getDate()).padStart(2, '0'); // Dodanie zera z przodu, jeśli dzień jest jednocyfrowy

const formattedDate = `${year}-${month}-${day}`;

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridMonth',
      },
      initialDate: formattedDate,
      firstDay: 1,
      navLinks: true, // can click day/week names to navigate views
      businessHours: true, // display business hours
      editable: true,
      selectable: true,
      dayMaxEvents: true,
      events: eventsarray,
      
    });

    calendar.render();
    });

</script>
</body>
</html>
  