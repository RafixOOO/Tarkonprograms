<?php
require_once('dbconnect.php');
require_once('auth.php');
if (isset($_POST['change_status'])) {
    $personId = $_POST['personid'];
    $identyfikator = $_POST['id'];

    $sql = "INSERT INTO dbo.PersonsID ([identyfikator], [PersonsID]) VALUES (?, ?)";
                $params = array($identyfikator, $personId);


                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();

}