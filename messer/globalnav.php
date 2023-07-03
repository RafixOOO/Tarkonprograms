
             <?php 
             require_once('../auth.php');
             
             if(!isLoggedIn()){
                 header("refresh: 15;");
             }
             ?>
             
             <h1 class="text-center"><b>Messer</b></h1>
              <div class="offcanvas offcanvas-start w-25" tabindex="-1" id="offcanvas" style = "max-width: 300px" data-bs-keyboard="false" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title d-none d-sm-block" id="offcanvas">Tarkon programs</h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0">
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-start" id="menu">
            <li class="nav-item">
                <a href="..\index.php" class="nav-link text-success">
                    <i class="fs-5 bi-house"></i><span class="ms-1 d-none d-sm-inline">Strona główna</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fs-5 bi-table"></i><span class="ms-1 d-none d-sm-inline">Messer</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown">
                    <li><a class="dropdown-item" href="index.php">Aktualne</a></li>
                    <li><a class="dropdown-item" href="wykonane.php">Wykonane</a></li>
                    <li><a class="dropdown-item" href="niewykonane.php">Niewykonane</a></li>
                </ul>
            </li>
            <?php if(isUserParts()){ ?>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fs-5 bi-grid"></i><span class="ms-1 d-none d-sm-inline">Parts</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
                    <li><a class="dropdown-item" href="../parts/index.php">Programy</a></li>
                    <li><a class="dropdown-item" href="../parts/upload.php">Wyślij</a></li>
                </ul>
            </li>
            <?php } ?>
            </div>
            
    <div class="offcanvas-footer" style="margin-top: auto; margin-left:10px; margin-bottom:10px">
        <?php if(!isLoggedIn()){ ?>
            <li class="nav-item">
                <a href="login.php" class="nav-link text-success">
                    <i class="fs-5 bi bi-person"></i><span class="ms-1 d-none d-sm-inline">Zaloguj się</span>
                </a>
            </li>
        <?php } else { ?>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle text-success" id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fs-5 bi bi-person"></i><span class="ms-1 d-none d-sm-inline"><?php echo $_SESSION['imie_nazwisko']; ?></span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
                    <li><a class="dropdown-item" href="../password.php">Zmień hasło</a></li>
                    <li><a class="dropdown-item" href="../logout.php">Wyloguj się</a></li>
                </ul>
            </li>
        <?php } ?>  
    </div>
</div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col min-vh-100 py-3">
            <!-- toggler -->
            <button class="btn" data-bs-toggle="offcanvas" id="button-container" data-bs-target="#offcanvas" role="button">
                <i class="bi bi-arrow-right-square-fill fs-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"></i>
            </button>

