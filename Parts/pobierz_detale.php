<?php

require_once("dbconnect.php");

$projekt1 = $_POST['projekt1'];
$zespol = $_POST['zespol'];

$sql = "SELECT DISTINCT p.Pozycja FROM dbo.Parts p WHERE NOT EXISTS (
    SELECT 1
    FROM dbo.PartArchive_Messer m
    WHERE p.Pozycja = m.PartName COLLATE Latin1_General_CS_AS
)
AND NOT EXISTS (
    SELECT 1
    FROM dbo.Product_V630 v
    WHERE p.Pozycja = v.Name
) 
AND p.Projekt = ? AND p.Zespol = ?";
$params = array($projekt1, $zespol);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$detale = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $detale[] = $row['Pozycja'];
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);


echo implode(',', $detale);
?>
