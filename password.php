<?php
require_once('auth.php');
require_once('dbconnect.php');
function updatePassword($username, $newPassword)
{
    $serverName = '10.100.100.48,49827';
$connectionOptions = array(
    "Database" => "PartCheck",
    "Uid" => "Sa",
    "PWD" => "Shark1445NE\$T"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
} 
    // Haszowanie nowego hasła
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Aktualizacja hasła w bazie danych
    $tsql = "UPDATE dbo.Persons SET [password] = ? WHERE [user] = ?";
    $params = array($hashedPassword, $username);
    $stmt = sqlsrv_query($conn, $tsql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    return true;
}

// Sprawdzenie, czy formularz został wysłany

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
            <div class="container">
        <h2 class="text-uppercase">Zmiana hasła</h2>
        <br />
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Aktualne hasło</label>
                <input type="password" class="form-control" id="current_password" name="current_password"
                    placeholder="Aktualne Hasło">
            </div>
            <br />
            <div class="form-group">
                <label for="new_password">Nowe hasło</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nowe Hasło">
            </div>
            <br />
            <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Zmień</button>
            <a href="index.php" type="button" class="btn btn-outline-success my-2 my-sm-0">Anuluj</a>
        </form>
            </div>
    
   
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sprawdzenie, czy użytkownik jest zalogowany
    if (isLoggedIn()) {
        // Pobranie danych z formularza
        $username = $_SESSION['username'];
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];

        // Sprawdzenie, czy aktualne hasło jest poprawne
        $tsql = "SELECT [password] FROM Persons WHERE [user] = ?";
        $params = array($username);
        $stmt = sqlsrv_query($conn, $tsql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($row && password_verify($currentPassword, $row['password'])) {
            // Aktualizacja hasła
            if (updatePassword($username, $newPassword)) {
                echo "<script>toastr.success('Hasło zostało zmienione!!!')</script>";
                echo '<meta http-equiv="refresh" content="2; URL=index.php">';
            } else {
                echo "<script>toastr.error('Wystąpił problem podczas zmiany hasła!!!')</script>";
            }
        } else {
            echo "<script>toastr.error('Aktualne hasło jest nieprawidłowe!!!')</script>";
        }
    } else {
        echo "<script>toastr.error('Użytkownik nie jest zalogowany!!!')</script>";
    }
    sqlsrv_close($conn);
}

?>
</html>