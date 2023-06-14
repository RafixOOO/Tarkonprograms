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
    <title>Logowanie</title>
    <?php include 'globalhead.php'; ?>
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
                <label for="password">Hasło</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło">
            </div>
            <br />
            <button type="submit" class="btn btn-primary">Zaloguj</button>
        </form>

    </div>
</body>

</html>