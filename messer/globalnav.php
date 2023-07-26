
             <?php 
             require_once('../auth.php');
             
             if(!isLoggedIn()){
                 header("refresh: 15;");
             }
             ?>
             
             <h1 class="text-center"><b>Messer</b></h1>
              <div class="offcanvas offcanvas-start w-25" tabindex="-1" id="offcanvas" style = "max-width: 300px" data-bs-keyboard="false" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title d-none d-sm-block" id="offcanvas">Tarkon programs <sup>1.47</sup></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0">
    <center>
    <?php if(!isLoggedIn()){ ?>

<a href="../login.php" class="nav-link text-success">
<img src="../static/person.svg"><br /></img></i><span class="ms-1 d-none d-sm-inline">Zaloguj się</span>
</a>
<?php } else { ?>
<a href="#" class="nav-link dropdown-toggle text-success" id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
<img src="../static/person.svg"><br /></img><span class="ms-1 d-none d-sm-inline"><?php echo $_SESSION['imie_nazwisko']; ?></span>
</a>
<ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
    <li><a class="dropdown-item" href="../password.php">Zmień hasło</a></li>
    <?php if(isUserAdmin()) { ?>
    <li><a class="dropdown-item" href="../zarzadzaj.php">Zarządzaj</a></li>
    <li><a class="dropdown-item" href="../logi.php">Logi</a></li>
    <?php } ?>
    <li><a class="dropdown-item" href="../logout.php">Wyloguj się</a></li>
</ul>
    
<?php } ?>  
</center>
<br />
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-start" id="menu">
            <li class="nav-item">
                <a href="..\index.php" class="nav-link text-success">
                <img src="../static/house.svg"></img><span class="ms-1 d-none d-sm-inline">Strona główna</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../static/table.svg"></img><span class="ms-1 d-none d-sm-inline">Messer</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown">
                    <li><a class="dropdown-item" href="main.php">Aktualne</a></li>
                    <li><a class="dropdown-item" href="wykonane.php">Wykonane</a></li>
                    <li><a class="dropdown-item" href="niewykonane.php">Niewykonane</a></li>
                </ul>
            </li>
            <?php if(isUserParts()){ ?>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown2" data-bs-toggle="dropdown" aria-expanded="false">
                    
                <img src="../static/dice-2.svg"></img><span class="ms-1 d-none d-sm-inline">V200</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown2">
                    <li><a class="dropdown-item" href="../v200/main.php">Otwory</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../static/grid.svg"></img><span class="ms-1 d-none d-sm-inline">Parts</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
                    <li><a class="dropdown-item" href="../parts/main.php">Programy</a></li>
                    <li><a class="dropdown-item" href="../parts/dozrobienia.php">Do zrobienia</a></li>
                    <li><a class="dropdown-item" href="../parts/upload.php">Wyślij</a></li>
                </ul>
            </li>
            <?php } ?>
            </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col min-vh-100 py-3">
            <!-- toggler -->
            <button class="btn" data-bs-toggle="offcanvas" id="button-container" data-bs-target="#offcanvas" role="button">
                <img src="../static/arrow-right-square-fill.svg" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"></img>
            </button>

