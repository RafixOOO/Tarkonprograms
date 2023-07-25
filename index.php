<!DOCTYPE html>
<html lang="en">
    <?php
        require_once 'auth.php';
    ?>
<head>
    <title>Tarkonprograms</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="fonts/icomoon/style.css">
  
    <link href='fullcalendar/packages/core/main.css' rel='stylesheet' />
    <link href='fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
    
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">
  <script src="blad.js"></script>
  <style>
    #button-container {
      position: fixed;
      top: 0;
      left: 0;
      padding: 10px;
    }
    </style>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<div class="offcanvas offcanvas-start w-25" tabindex="-1" id="offcanvas" style = "max-width: 300px" data-bs-keyboard="false" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title d-none d-sm-block" id="offcanvas">Tarkon programs <sup>1.46</sup></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0">
    <center>
    <?php if(!isLoggedIn()){ ?>

<a href="login.php" class="nav-link text-success">
    <i class="fs-5 bi bi-person"><br /></i><span class="ms-1 d-none d-sm-inline">Zaloguj się</span>
</a>
<?php } else { ?>
<a href="#" class="nav-link dropdown-toggle text-success" id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="fs-5 bi bi-person"><br /></i><span class="ms-1 d-none d-sm-inline"><?php echo $_SESSION['imie_nazwisko']; ?></span>
</a>
<ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
    <li><a class="dropdown-item" href="password.php">Zmień hasło</a></li>
    <?php if(isUserAdmin()) { ?>
    <li><a class="dropdown-item" href="zarzadzaj.php">Zarządzaj</a></li>
    <li><a class="dropdown-item" href="logi.php">Logi</a></li>
    <?php } ?>
    <li><a class="dropdown-item" href="logout.php">Wyloguj się</a></li>
</ul>
    
<?php } ?>  
</center>
<br />
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-start" id="menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link text-success">
                    <i class="fs-5 bi-house"></i><span class="ms-1 d-none d-sm-inline">Strona główna</span>
                </a>
            </li>

            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    
                    <i class="fs-5 bi-table"></i><span class="ms-1 d-none d-sm-inline">Messer</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown">
                    <li><a class="dropdown-item" href="messer/main.php">Aktualne</a></li>
                    <li><a class="dropdown-item" href="messer/wykonane.php">Wykonane</a></li>
                    <li><a class="dropdown-item" href="messer/niewykonane.php">Niewykonane</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown2" data-bs-toggle="dropdown" aria-expanded="false">
                    
                    <i class="fs-5 bi-dice-2"></i><span class="ms-1 d-none d-sm-inline">V200</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown2">
                    <li><a class="dropdown-item" href="v200/main.php">Otwory</a></li>
                </ul>
            </li>
 
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fs-5 bi-grid"></i><span class="ms-1 d-none d-sm-inline">Parts</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
                    <li><a class="dropdown-item" href="parts/main.php">Programy</a></li>
                    <li><a class="dropdown-item" href="parts/dozrobienia.php">Do zrobienia</a></li>
                    <?php if(isLoggedin()){ ?>
                    <li><a class="dropdown-item" href="parts/upload.php">Wyślij</a></li>
                    <?php } ?>
                </ul>
                
                
            </li>

            </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col min-vh-100 py-3">
            <!-- toggler -->
            <button class="btn" data-bs-toggle="offcanvas" id="button-container" data-bs-target="#offcanvas" role="button">
                <i class="bi bi-arrow-right-square-fill fs-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"></i>
            </button>

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
  