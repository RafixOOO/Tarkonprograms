<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once("globalhead.php"); ?>

</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
  
<?php require_once("globalnav.php"); ?>
<button class="btn btn-primary float-end" onclick="searchByGroupId()">Szukaj</button>
<select class="form-control w-25 float-end" name="evens" id="groupIdInput">
  <option value="v630">V630</option>
  <option value="messer">Messer</option>
  <option value="Pila">Pila</option>
  <option value="Recznie">Recznie</option>
  <option value="Kooperacyjnie">Kooperacyjnie</option>
</select>
    <br /><br />
<div style="width:85%;margin-left:auto;margin-right:auto;" id='calendar'></div>
<br />
<script src='dist/index.global.js'></script>
<script>
    var calendar;
    var eventsarray = [];

    function searchByGroupId() {
      var groupIdInput = document.getElementById('groupIdInput').value;
      var filteredEvents;

      if (groupIdInput === 'all') {
        // Wyświetl wszystkie wydarzenia
        filteredEvents = eventsarray;
      } else {
        // Wyświetl wydarzenia tylko dla wybranego groupId
        filteredEvents = eventsarray.filter(function(event) {
          return event.groupId === groupIdInput;
        });
      }

      calendar.removeAllEvents();
      calendar.addEventSource(filteredEvents);
      calendar.render();
    }

    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');

    const formattedDate = `${year}-${month}-${day}`;

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: 'title',
          center: '',
          right: 'prev,next',
        },
        initialDate: formattedDate,
        firstDay: 1,
        navLinks: false,
        businessHours: true,
        editable: false,
        selectable: false,
        dayMaxEvents: true,
        events: eventsarray,
        eventSources: [{
          url: 'fetch_events.php', // Adres pliku do pobierania danych z bazy
          method: 'GET', // Metoda żądania
          extraParams: {
            startDate: function() {
              var view = calendar.view;
              return view.activeStart.format('YYYY-MM-DD');
            },
            endDate: function() {
              var view = calendar.view;
              return view.activeEnd.format('YYYY-MM-DD');
            },
          },
          failure: function(jqXHR, textStatus, errorThrown) {
            alert('Wystąpił błąd podczas pobierania danych.');
            console.log(jqXHR.responseText);
          },
          success: function(data) {
            // Aktualizuj zawartość eventsarray
            eventsarray = data;
            // Wyświetl wszystkie wydarzenia na kalendarzu
            calendar.removeAllEvents();
            calendar.addEventSource();
            calendar.render(eventsarray);
          }
        }],
        dateClick: function(info) {
          var selectedDate = info.date;
          var year = selectedDate.getFullYear();
          var month = String(selectedDate.getMonth() + 1).padStart(2, '0');
          var firstDay = year + '-' + month + '-01';
          var lastDay = year + '-' + month + '-' + new Date(year, selectedDate.getMonth() + 1, 0).getDate();
          calendar.refetchEvents(); // Odśwież dane w kalendarzu
        }
      });

      calendar.render();
    });
  </script>
</body>
</html>
  