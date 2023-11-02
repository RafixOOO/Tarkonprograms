<?php 
require_once("dbconnect.php");

$sqlcut = "SELECT ID, PROGRAM, PROJEKT, OPIS, DLUGOSC, RESZTKI, CZESC, CZDLU, CNT, checkpr
FROM PartCheck.dbo.cutlogic
Order by checkpr asc";
$datacut = sqlsrv_query($conn, $sqlcut); 

?>