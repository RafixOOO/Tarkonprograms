<?php 

require_once("../dbconnect.php");


$userNumber = $_POST['number'];
$sql = "SELECT * FROM dbo.PersonsID pid inner join dbo.Persons p on p.Id = pid.PersonsID WHERE pid.identyfikator = ?";
$params = array($userNumber);
$stmt1 = sqlsrv_query($conn, $sql, $params);
$imie;
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $imie=$row['imie_nazwisko'];
    $identyfikator=$row['identyfikator'];
}
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$stmt = sqlsrv_query($conn, $sql, $params, $options);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rowCount = sqlsrv_num_rows($stmt);
if ($rowCount > 0) {
    // Numer znajduje się w bazie danych
    $sql1 = "SELECT h.Project from dbo.PersonsID p 
inner join 
dbo.HrapWorkTime h on h.PersonID=p.PersonsID 
where p.identyfikator = ? and h.cuce_date_to is null and h.cuce_time_to is null";
    $stmt2 = sqlsrv_query($conn, $sql1, $params);
    $strona;
    $rowCount1 = sqlsrv_has_rows($stmt2) ? 1 : 0; // Alternatywa, jeśli num_rows nie działa
    $strona = ($rowCount1 > 0) ? "worktime.php?finish=1&identyfikator=$identyfikator" : "main.php";

    echo "true," . $imie . "," . $strona.", ".$identyfikator;
} else {
    // Numer nie został odnaleziony w bazie danych
    echo "false";
}


?>