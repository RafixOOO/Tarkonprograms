<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once("globalhead.php"); ?>


    
    <link rel="stylesheet" href="fonts/icomoon/style.css">
  
    <link href='fullcalendar/packages/core/main.css' rel='stylesheet' />
    <link href='fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
    
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php require_once("globalnav.php"); ?>

    <div id='calendar'></div>

</div>
</div>
</div>
<h1 style=" position: absolute;
      bottom: 2%;
      right: 0;
      color: white;
      padding: 10px;">Wersja: 1.25</h1>
</body>

<script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script src='fullcalendar/packages/core/main.js'></script>
    <script src='fullcalendar/packages/interaction/main.js'></script>
    <script src='fullcalendar/packages/daygrid/main.js'></script>

    <script>
        function getCurrentDate() {
  const currentDate = new Date();
  const year = currentDate.getFullYear();
  const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Dodajemy +1, ponieważ styczeń jest oznaczony jako 0, luty jako 1, itd.
  const day = String(currentDate.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}
      document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        header: {
      left: 'title',
      center: '',
      right: 'addEventButton prev,next'
    },
      plugins: [ 'interaction', 'dayGrid' ],
      defaultDate: getCurrentDate(),
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: [
        //{
         // title: 'Test1',
       //   start: '2023-07-01',
        //  end: '2023-07-05'
       // },
        //{
        //  title: 'Test2',
         // url: 'http://messer.local/parts/main.php',
        //  start: '2023-07-01',
         // end: '2023-07-03'
        //}
    ],
      firstDay: 1
    });

    calendar.setOption('customButtons', {
        addEventButton: {
        text: 'Dodaj', // Tekst na przycisku
        click: function() {
          // Tu możesz umieścić kod obsługujący akcję po kliknięciu przycisku "Dodaj"
          // Na przykład otwarcie okna modalnego z formularzem do dodawania nowego wydarzenia
          // lub wywołanie innych funkcji odpowiednich dla Twojego przypadku użycia.
          alert('Kliknięto przycisk "Dodaj"');
        }
      },
    today: {
      text: 'today',
      click: function() {
        // Pusta funkcja - nic nie robi, dlatego przycisk jest niewidoczny
      }
    }
  });

    calendar.render();
  });

    </script>

    <script src="js/main.js"></script>

</html>
  