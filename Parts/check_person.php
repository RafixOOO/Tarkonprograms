<?php 

require_once("dbconnect.php");

$userNumber = $_POST['number'];
$sql = "SELECT * FROM dbo.Persons WHERE identyfikator = ?";
$params = array($userNumber);
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$stmt = sqlsrv_query($conn, $sql, $params, $options);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rowCount = sqlsrv_num_rows($stmt);
if ($rowCount > 0) {
    // Numer znajduje się w bazie danych
    echo "true";
} else {
    // Numer nie został odnaleziony w bazie danych
    echo "false";
}


?>