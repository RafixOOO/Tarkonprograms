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
<div style="width:85%;margin-left:auto;margin-right:auto;" id='calendar'></div>
<br />
<script src='dist/index.global.js'></script>
<script>

const currentDate = new Date();
const year = currentDate.getFullYear();
const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Dodanie zera z przodu, jeśli miesiąc jest jednocyfrowy
const day = String(currentDate.getDate()).padStart(2, '0'); // Dodanie zera z przodu, jeśli dzień jest jednocyfrowy

const formattedDate = `${year}-${month}-${day}`;

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridMonth'
      },
      initialDate: formattedDate,
      firstDay: 1,
      navLinks: true, // can click day/week names to navigate views
      businessHours: true, // display business hours
      editable: true,
      selectable: true,
      events: [
        {
          title: 'Business Lunch',
          start: '2023-01-03T13:00:00',
          constraint: 'businessHours'
        },
        {
          title: 'Meeting',
          start: '2023-01-13T11:00:00',
          constraint: 'availableForMeeting', // defined below
          color: '#257e4a'
        },
        {
          title: 'Conference',
          start: '2023-01-18',
          end: '2023-01-20'
        },
        {
          title: 'Party',
          start: '2023-01-29T20:00:00'
        },

        // areas where "Meeting" must be dropped
        {
          groupId: 'availableForMeeting',
          start: '2023-01-11T10:00:00',
          end: '2023-01-11T16:00:00',
          display: 'background'
        },
        {
          groupId: 'availableForMeeting',
          start: '2023-01-13T10:00:00',
          end: '2023-01-13T16:00:00',
          display: 'background'
        },

        // red areas where no events can be dropped
        {
    start: year+'-01-01',
    end: year+'-01-01',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-01-06',
    end: year+'-01-06',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-04-04',
    end: year+'-04-05',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-05-01',
    end: year+'-05-01',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-05-03',
    end: year+'-05-03',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-05-23',
    end: year+'-05-23',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-08-15',
    end: year+'-08-15',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-11-01',
    end: year+'-11-01',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-11-11',
    end: year+'-11-11',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-12-25',
    end: year+'-12-25',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  },
  {
    start: year+'-12-26',
    end: year+'-12-26',
    overlap: false,
    display: 'background',
    color: '#ff9f89'
  }
      ]
    });

    calendar.render();
  });

</script>
</body>
</html>
  