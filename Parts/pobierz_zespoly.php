<?php

require_once("dbconnect.php");

$projekt1 = $_POST['projekt1'];
$sql = "SELECT DISTINCT p.Zespol as zespol FROM dbo.Parts p WHERE NOT EXISTS (
    SELECT 1
    FROM dbo.PartArchive_Messer m
    WHERE p.Pozycja = m.PartName COLLATE Latin1_General_CS_AS
)
AND NOT EXISTS (
    SELECT 1
    FROM dbo.Product_V630 v
    WHERE p.Pozycja = v.Name
) 
AND p.Projekt = '$projekt1'";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$zespoly = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $zespoly[] = $row['zespol'];
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo implode(',', $zespoly);
?>