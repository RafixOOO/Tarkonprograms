<?php
session_start();
requireLogin();
function isLoggedIn()
{
    return isset($_SESSION['username']);
}

function requireLogin()
{
    if (isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

$users = array(
    'Admin' => array('password' => 'AdminTarkon##', 'role' => 'admin'),
);

function login($username, $password)
{
    global $users;

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];
        return true;
    }

    return false;
}
?>
<!DOCTYPE html>
<html>

<head>
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
    <title>Tarkon programs</title>
</head>
<body class="p-3 mb-2 bg-light bg-gradient text-dark">
<div class="offcanvas offcanvas-start w-25" tabindex="-1" id="offcanvas" data-bs-keyboard="false" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title d-none d-sm-block" id="offcanvas">Tarkon programs</h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0">
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
                    <li><a class="dropdown-item" href="messer/index.php">Aktualne</a></li>
                    <li><a class="dropdown-item" href="messer/wykonane.php">Wykonane</a></li>
                    <li><a class="dropdown-item" href="messer/niewykonane.php">Niewykonane</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link dropdown-toggle  text-success " id="dropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fs-5 bi-grid"></i><span class="ms-1 d-none d-sm-inline">Parts</span>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown1">
                    <li><a class="dropdown-item" href="parts/index.php">Programy</a></li>
                    <li><a class="dropdown-item" href="parts/upload.php">Wyślij</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col min-vh-100 py-3">
            <!-- toggler -->
            <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" role="button">
                <i class="bi bi-arrow-right-square-fill fs-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"></i>
            </button>
            <div class="container">
        <h2 class="text-uppercase">Login</h2>
        <br />
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nazwa użytkownika</label>
                <input type="text" class="form-control" id="username" name="username"
                    placeholder="Wpisz nazwę użytkownika">
            </div>
            <br />
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło">
            </div>
            <br />
            <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Zaloguj</button>
            <button type="reset" class="btn btn-outline-success my-2 my-sm-0">Wyczyść</button>
        </form>
            </div>
            </div>
    </div>
</div>
   
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        echo "<script>toastr.success('Zalogowano się pomyślnie!!!')</script>";
        echo '<meta http-equiv="refresh" content="2; URL=index.php">';
    } else {
        echo "<script>toastr.error('Błędne dane logowania!!!')</script>";
    }
}
?>
</html>