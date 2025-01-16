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
    $_SESSION['ID'] = $row['Id'];
            $_SESSION['role_messer'] = $row['role_messer'];
            $_SESSION['role_parts'] = $row['role_parts'];
            $_SESSION['role_admin'] = $row['role_admin'];
            $_SESSION['role_parts_kier'] = $row['role_parts_kier'];
            $_SESSION['imie_nazwisko'] = $row['imie_nazwisko'];
            $_SESSION['role_cutlogic'] = $row['role_cutlogic'];
            $_SESSION['sidebar'] = $row['sidebar'];
        return true;
    
    }
    else if ($row) {
        $hashedPassword = $row['password'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['ID'] = $row['Id'];
            $_SESSION['username'] = $row['user'];
            $_SESSION['role_messer'] = $row['role_messer'];
            $_SESSION['role_parts'] = $row['role_parts'];
            $_SESSION['role_admin'] = $row['role_admin'];
            $_SESSION['role_cutlogic'] = $row['role_cutlogic'];
            $_SESSION['role_parts_kier'] = $row['role_parts_kier'];
            $_SESSION['imie_nazwisko'] = $row['imie_nazwisko'];
            $_SESSION['sidebar'] = $row['sidebar'];
            return true;
        }
    }else{
        return false;
    }
    
    
}

function logUserActivity($username, $operation) {
    $logFilePath = 'dziennik.log';
    $logMessage = "[" . date('Y-m-d H:i:s') . "],$username,$operation" . PHP_EOL;

    // Otwarcie pliku dziennika w trybie dołączania
    $file = fopen($logFilePath, 'a');

    // Zapisanie komunikatu do pliku dziennika
    fwrite($file, $logMessage);

    // Zamknięcie pliku dziennika
    fclose($file);
}

?>
<!DOCTYPE html>
<html>
<title>Tarkonprograms</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="shortcut icon" href="static/clipboard-data.svg">
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

<link rel="stylesheet" href="static/toastr.min.css">
<script src="static/jquery.min.js"></script>
<script src="static/jquery-ui.min.js"></script>
<script src="static/toastr.min.js"></script>
<script src="static/jquery-3.6.0.min.js"></script>
<script src="static/jquery.min.js"></script>
<script src="static/darkmode-js.min.js"></script>
<head>
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
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)===true) {
        logUserActivity($_SESSION['imie_nazwisko'],'Logowanie');
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