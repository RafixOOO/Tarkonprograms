<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['username']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

$users = array(
    'Admin' => array('password' => 'AdminTarkon##', 'role' => 'admin'),
    '' => array('password' => '', 'role' => 'user'),
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        echo "<script>alert('Błędne dane logowania');</script>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark">
    <div class="container">
        <br />
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
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło">
            </div>
            <br />
            <button type="submit" class="btn btn-primary">Zaloguj</button>
        </form>

    </div>
</body>

</html>