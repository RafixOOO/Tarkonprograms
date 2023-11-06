<?php 
require_once("dbconnect.php");

$sqlcut = "SELECT ID, PROGRAM, PROJEKT, OPIS, DLUGOSC, RESZTKI, CZESC, CZDLU, CNT
FROM PartCheck.dbo.cutlogic";
$datacut = sqlsrv_query($conn, $sqlcut); 

?>