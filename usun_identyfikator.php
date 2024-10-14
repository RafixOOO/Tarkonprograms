<?php
require_once('dbconnect.php');
require_once('auth.php');
if (isset($_POST['change_status'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM PartCheck.dbo.PersonsID WHERE id = ?;";

$params = array($id); // Przekazanie wartości id jako parametru

$stmt = sqlsrv_query($conn, $sql, $params); // Przekazanie zapytania wraz z parametrami

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true)); // Obsługa błędów
}
                header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();

}