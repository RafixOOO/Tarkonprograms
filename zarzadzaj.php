<!DOCTYPE html>
<html lang="en">
    <?php
        require_once 'auth.php';
        require_once('dbconnect.php');
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
        <h6 class="offcanvas-title d-none d-sm-block" id="offcanvas">Tarkon programs <sup>1.25</sup></h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0">
    <center>
    <?php if(!isLoggedIn()){ ?>

<a href="../login.php" class="nav-link text-success">
    <i class="fs-5 bi bi-person"></i><span class="ms-1 d-none d-sm-inline">Zaloguj się</span>
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
                    <li><a class="dropdown-item" href="parts/upload.php">Wyślij</a></li>
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
            <div class="container">
            <div class="table-responsive">
            <a href="dodaj.php" class="btn btn-success float-end">Dodaj</a>
            
            <table class="table table-sm">
  <thead>
    <tr>
      <th scope="col">Identyfikator</th>
      <th scope="col">Imię i nazwisko</th>
      <th scope="col">Login</th>
      <th scope="col">Messer</th>
      <th scope="col">Parts</th>
      <th scope="col">Parts Kierownik</th>
      <th scope="col">Admin</th>
      <th scope="col">Zarządzaj</th>
      
    </tr>
  </thead>
  <tbody>
    <?php 
    
        $sql = "Select * from dbo.Persons";
        $stmt = sqlsrv_query($conn, $sql);
        while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {           
    ?>
    <tr>
        <td><?php echo $data['identyfikator'] ?></td>
        <td><?php echo $data['imie_nazwisko'] ?></td>
        <td><?php echo $data['user'] ?></td>
        <?php if($data['user']!=""){ ?>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_messer">
                <?php if( $data['role_messer']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_parts">
                <?php 
                if($data['role_parts_kier']==1){
                    if( $data['role_parts']==1){ ?>
                        <button type="submit" name="change_status" class="btn btn-success" disabled></button><?php } else { ?>
                            <button type="submit" name="change_status" class="btn btn-danger" disabled></button> <?php } ?>
                
                <?php } else {
                if( $data['role_parts']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php }} ?>
            </form>
        </td>
        <td>
        <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_parts_kier">
                
                <?php if( $data['role_parts_kier']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_admin">
                
                <?php if( $data['role_admin']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        
        <?php }  ?>
        <td>
            <?php if($data['user']!=""){ ?>
            <form method="post" action="usun_haslo.php" style="float: left;">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <button type="submit" name="usun_haslo" class="btn btn-warning">Usuń hasło</button>
            </form>
            <form method="post" action="usun_konto.php" style="float: left; margin-left:2%;">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <button type="submit" name="usun_konto" class="btn btn-warning">Usuń konto</button>
            </form>
            <div style="clear:both;"></div>
            <?php } else{ ?>
                <td></td>
                <td></td>
                <td></td>
                <td>
                <form method="post" action="usun_konto.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <button type="submit" name="usun_konto" class="btn btn-warning">Usuń konto</button>
            </form>
                </td>
                <?php } ?>
            
        </td>
    </tr>
    
    <?php } ?>
  </tbody>
</table>
</div>
        </div>
</div>
</div>
</div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var offcanvas = new bootstrap.Offcanvas(document.getElementById("offcanvas"));
        offcanvas.show();
    });
</script>
</html>