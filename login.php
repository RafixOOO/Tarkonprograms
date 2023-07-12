<?php
session_start();

function login($username, $password)
{
    require('dbconnect.php');

    $tsql = "SELECT * FROM dbo.Persons WHERE [user] = ?";
    $params = array($username);
    $getResults = sqlsrv_query($conn, $tsql, $params);
    $row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC);
    if(sqlsrv_fetch($getResults)=== false){
        return "brak";
    }
    else if($row['password']==""){
    // Haszowanie nowego hasła
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Aktualizacja hasła w bazie danych
    $tsql = "UPDATE dbo.Persons SET [password] = ? WHERE [user] = ?";
    $params = array($hashedPassword, $username);
    $stmt = sqlsrv_query($conn, $tsql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), false));
    }

    

    $_SESSION['username'] = $row['user'];
            $_SESSION['role_messer'] = $row['role_messer'];
            $_SESSION['role_parts'] = $row['role_parts'];
            $_SESSION['role_admin'] = $row['role_admin'];
            $_SESSION['role_parts_kier'] = $row['role_parts_kier'];
            $_SESSION['imie_nazwisko'] = $row['imie_nazwisko'];
        return true;
    
    }
    else if ($row) {
        $hashedPassword = $row['password'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $row['user'];
            $_SESSION['role_messer'] = $row['role_messer'];
            $_SESSION['role_parts'] = $row['role_parts'];
            $_SESSION['role_admin'] = $row['role_admin'];
            $_SESSION['role_parts_kier'] = $row['role_parts_kier'];
            $_SESSION['imie_nazwisko'] = $row['imie_nazwisko'];
            return true;
        }
    }else{
        return false;
    }
    
    
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
    <script src="blad.js"></script>
</head>
<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">

            <div class="container">
        <h2 class="text-uppercase">Login</h2>
        <br />
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nazwa użytkownika</label>
                <input type="text" class="form-control" id="username" name="username"
                    placeholder="Wpisz nazwę użytkownika" required>
            </div>
            <br />
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło" required>
            </div>
            <br />
            <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Zaloguj</button>
            <a href="index.php" type="button" class="btn btn-outline-success my-2 my-sm-0">Anuluj</a>
        </form>
            </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)===true) {
        echo "<script>toastr.success('Zalogowano się pomyślnie!!!')</script>";
        echo '<meta http-equiv="refresh" content="2; URL=index.php">';
    } else if(login($username, $password)==="brak"){
        echo "<script>toastr.error('Brak użytkownika!!!')</script>";
    } else {
        echo "<script>toastr.error('Błędne dane logowania!!!')</script>";
    }
}
?>
</html>