<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
require_once('dbconnect.php');
$message = $_POST['message'];
$user= $_POST['user'];
$currentDateTime = date('Y-m-d H:i:s');
$sql="INSERT INTO PartCheck.dbo.messages_messer
(massage, osoba, [date])
VALUES('$message', '$user', '$currentDateTime');";
$result = sqlsrv_query($conn, $sql);
if ($result === false) {
    echo "Error: " . $sql . "<br>" . print_r(sqlsrv_errors(), true);
} else {
    echo "Message sent successfully";
}
}