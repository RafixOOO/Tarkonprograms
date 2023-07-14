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
<style>
    body {
      margin: 0;
      padding: 0;
      position: relative;
      background-image: url('tarkon.jpg');
      background-size: cover;
      background-position: center;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('tarkon.jpg'); /* Kolor zamazania */
      background-repeat: no-repeat;
      background-size: 100% 100%;
      filter: blur(10px);
      z-index: -1;
    }

    .content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    .content h1 {
      font-size: 36px;
    }
  </style>
  <script src="blad.js"></script>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<div class="offcanvas offcanvas-start w-25" tabindex="-1" id="offcanvas" style = "max-width: 300px" data-bs-keyboard="false" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title d-none d-sm-block" id="offcanvas">Tarkon programs <sup>1.25</sup></h6>
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
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fs-5 bi-grid"></i><span class="ms-1 d-none d-sm-inline">Parts</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
                    <li><a class="dropdown-item" href="parts/main.php">Programy</a></li>
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
            <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" role="button">
                <i class="bi bi-arrow-right-square-fill fs-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"></i>
            </button>
            <div class="content">
            <img src="tarkon.jpg" alt="Obrazek">
            <div class="content">
            <h1 style="text-align: center; font-size: 8.5em; ">
            <span style="display: block;color: #1a4585;border-width: 2px; text-shadow: 2px 2px 2px black;"><b>Tarkon</span>
            <span style="margin-top: 20%; display: block;color: #ffda07; text-shadow: 2px 2px 2px black"><i>programs</i></b></span>
            </h1>

            </div>
</div>
</div>
</div>
<h1 style=" position: absolute;
      bottom: 2%;
      right: 0;
      color: white;
      padding: 10px;">Wersja: 1.25</h1>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var offcanvas = new bootstrap.Offcanvas(document.getElementById("offcanvas"));
        offcanvas.show();
    });

</script>

</html>
  