<?php 
require_once('dbconnect.php');
require_once('auth.php');
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
</head id="error-container">
<body class="p-3 mb-2 bg-light bg-gradient text-dark">
            <div class="container">
        <h2 class="text-uppercase">Dodaj</h2>
        <br />
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Identyfikator</label>
                <input type="number" class="form-control" id="id" name="id"
                    placeholder="Identyfikator" required>
            </div>
            <br />
            <div class="form-group">
                <label for="new_password">Imię i Nazwisko</label>
                <input type="text" class="form-control" id="nazwa" name="nazwa" placeholder="Imię i Nazwisko" required>
            </div>
            <br />
            <div class="form-group">
                <label for="new_password">Login (opcjonalne)</label>
                <input type="text" class="form-control" id="login" name="login" placeholder="Login">
            </div>
            <br />
            <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Dodaj</button>
            <a href="zarzadzaj.php" type="button" class="btn btn-outline-success my-2 my-sm-0">Anuluj</a>
        </form>
            </div>
    
   
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<?php 

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'];
                $nazwa = $_POST['nazwa'];
                $login = $_POST['login'];

                $sql = "INSERT INTO dbo.Persons ([identyfikator], [imie_nazwisko], [user]) VALUES (?, ?, ?)";
                $params = array($id, $nazwa, $login);


                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                logUserActivity($_SESSION['imie_nazwisko'],'Dodanie użytkownika:'.$_POST['nazwa']);
                echo "<script>toastr.success('Pomyślnie dodano użytkownika!!!')</script>";
                    echo '<meta http-equiv="refresh" content="2; URL=zarzadzaj.php">';
                sqlsrv_close($conn);
            }
    

?>
</html>