<?php require_once 'auth.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <!-- Container wrapper -->
  <a class="navbar-brand" style="margin-left:2%;">Tarkon programs <sup>2.3</sup></a>
  <button style="margin-right:2%;" class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
      aria-label="Toggle navigation">
      <img src="static/menu.svg"></img>
    </button>
  <div class="container-xxl">
    <!-- Navbar brand -->



    <!-- Collapsible wrapper -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left links -->
      <ul class="navbar-nav me-auto d-flex flex-row mt-3 mt-lg-0">
        <li class="nav-item text-center mx-2 mx-lg-1">
          <a class="nav-link active" aria-current="page" href="index.php">
            Strona główna
          </a>
        </li>

        <li class="nav-item dropdown text-center mx-2 mx-lg-1">
          <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown1" role="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            Messer
          </a>
          <!-- Dropdown menu -->
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown1">
          <li><a class="dropdown-item" href="messer/main.php">Aktualne</a></li>
            <li><a class="dropdown-item" href="messer/wykonane.php">Zakończone</a></li>
            <li><a class="dropdown-item" href="messer/archiwum.php">Archiwalne</a></li>
          </ul>
        </li>

        <li class="nav-item text-center mx-2 mx-lg-1">
          <a class="nav-link" aria-current="page" href="v200/main.php">
            V200
          </a>
        </li>

        <li class="nav-item dropdown text-center mx-2 mx-lg-1">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            Parts
          </a>
          <!-- Dropdown menu -->
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="parts/main.php">Programy</a></li>
            <li><a class="dropdown-item" href="parts/dozrobienia.php">Gotowe</a></li>
            <?php if(isUserParts()){ ?>
            <li><a class="dropdown-item" href="parts/upload.php">Wyślij</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php if(isUserCutlogic()){ ?>
        <li class="nav-item text-center mx-2 mx-lg-1">
          <a class="nav-link" aria-current="page" href="cutlogic/main.php">
            CutLogic
          </a>
        </li>
        <?php } ?>
      </ul>
      <!-- Left links -->

      <!-- Right links -->
      <ul class="navbar-nav ms-auto d-flex flex-row mt-3 mt-lg-0">
        <li class="nav-item dropdown text-center mx-2 mx-lg-1">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-auto-close="outside" data-bs-toggle="dropdown"
            aria-expanded="false">

            <?php if(isLoggedIn()) { ?>
              <?php echo $_SESSION['imie_nazwisko']; ?>
            <?php } else { ?>
              Zaloguj się

            <?php } ?> 
          </a>
          <!-- Dropdown menu -->
          
          <ul class="dropdown-menu dropdown-menu-dark" style="left: -35%;" aria-labelledby="navbarDropdown">
            
              <li class="dropstart"><a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false" >Ustawienia</a>
            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown2">
            <?php if(isLoggedIn()) { ?>
            <li><a class="dropdown-item" href="password.php">Zmień hasło</a></li>
            <?php } ?>
            <li><a class="dropdown-item" id="darkModeButton" href="#">Tryb ciemny</a></li>
            </ul>
            
            <?php if(isUserAdmin()) { ?>
              <li class="dropstart"><a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false" >Panel admina</a>
            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown2">
            <li><a class="dropdown-item" href="zarzadzaj.php">Zarządzaj</a></li>
            <li><a class="dropdown-item" href="logi.php">Logi</a></li>
            </ul>
          </li>
            
            <?php } ?>
            <li class="dropdown-divider"></li>
            <?php if(isLoggedIn()) { ?>
            <li><a class="dropdown-item" href="logout.php">Wyloguj się</a></li>
            <?php } ?>
            <?php if(!isLoggedIn()) { ?> 
              <li><a class="dropdown-item" href="login.php">Zaloguj się</a></li>
              <?php } ?> 
          </ul>
          
        </li>
      </ul>
      <!-- Right links -->
    </div>
    <!-- Collapsible wrapper -->
  </div>
  <!-- Container wrapper -->
</nav>
<br />
<!-- Navbar -->
