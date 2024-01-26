<?php
require_once('auth.php');
require_once('dbconnect.php');
function updateusername($username, $newPassword)
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


    // Aktualizacja hasła w bazie danych
    $tsql = "UPDATE dbo.Persons SET [user] = ? WHERE [user] = ?";
$params = array($newPassword, $username);
    $stmt = sqlsrv_query($conn, $tsql, $params);
    $_SESSION['username']=$newPassword;

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
<?php require_once("globalhead.php"); ?>
</head>
<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
            <div class="container">
        <h2 class="text-uppercase">Zmiana nazwy użytkownika</h2>
        <br />
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Nowa nazwa użytkownika</label>
                <input type="text" class="form-control" id="current_password" name="current_password"
                    placeholder="Nazwa użytkownika">
            </div>
            <br />
            <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Zmień</button>
            <a href="#" onclick="goBack()" type="button" class="btn btn-outline-success my-2 my-sm-0">Anuluj</a>
        </form>
            </div>

            <input type="hidden" id="darkModeButton" />
</body>
<script>
    function goBack() {
        // Przejdź do poprzedniej strony w historii przeglądarki
        window.history.back();
    }
  </script>
<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sprawdzenie, czy użytkownik jest zalogowany
    if (isLoggedIn()) {
        // Pobranie danych z formularza
        $username = $_SESSION['username'];
        $currentPassword = $_POST['current_password'];

        // Sprawdzenie, czy aktualne hasło jest poprawne
            // Aktualizacja hasła
            if (updateusername($username, $currentPassword)) {
                logUserActivity($_SESSION['imie_nazwisko'],'Zmienił nazwe użytkownika');
                echo "<script>toastr.success('Nazwa użytkownika została zmieniona !!!')</script>";
                echo '<meta http-equiv="refresh" content="2; URL=index.php">';
                
            } else {
                echo "<script>toastr.error('Wystąpił problem podczas zmiany nazwy użytkownika!!!')</script>";
            }


    } else {
        echo "<script>toastr.error('Użytkownik nie jest zalogowany!!!')</script>";
    }
    sqlsrv_close($conn);
}

?>
<script src="static/dark.js"></script>
</html>