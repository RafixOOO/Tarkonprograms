<?php
require_once("dbconnect.php");

$sqlother = "SELECT
p.[Projekt] AS ProjectName,
STUFF((SELECT ' | ' + Zespol
       FROM dbo.Parts
       WHERE [Projekt] = p.[Projekt] AND [Pozycja] = p.[Pozycja]
       FOR XML PATH('')), 1, 3, '') AS aggregated_zespol,
p.[Pozycja] AS Name
FROM
dbo.Parts p
WHERE
NOT EXISTS (
    SELECT 1
    FROM dbo.PartArchive_Messer m
    WHERE p.Pozycja = m.PartName COLLATE Latin1_General_CS_AS
)
AND NOT EXISTS (
    SELECT 1
    FROM dbo.Product_V630 v
    WHERE p.Pozycja = v.Name
)
GROUP BY
p.[Projekt], p.[Pozycja]";
$dataother = sqlsrv_query($conn, $sqlother);
?>