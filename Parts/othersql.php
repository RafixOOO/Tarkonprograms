<?php
require_once("dbconnect.php");

$sqlother = "SELECT
p.Id_import as import
,Count(p.[Zespol]) as liczba_zespoly
,v.[AmountNeeded] as ilosc_v200
,(
SELECT SUM(v1.[AmountDone])
FROM [PartCheck].[dbo].[Product_V200] v1
WHERE v1.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
) as ilosc_v200_zre , 
p.[Pozycja] AS Detal,
p.[Projekt] AS ProjectName,
p.[Status] as status,
STUFF((SELECT ' | ' + Zespol
       FROM dbo.Parts
       WHERE [Projekt] = p.[Projekt] AND [Pozycja] = p.[Pozycja]
       FOR XML PATH('')), 1, 3, '') AS zespol,
 (SELECT SUM(p2.Ilosc)
    FROM dbo.Parts p2
    WHERE p.[Pozycja] = p2.[Pozycja]
) AS ilosc,
(
    SELECT SUM(p2.Dlugosc)
    FROM dbo.Parts p2
    WHERE p.[Pozycja] = p2.[Pozycja]
) AS dlugosc,
p.Ciezar AS Ciezar,
(
    SELECT SUM(p2.Calk_ciez)
    FROM dbo.Parts p2
    WHERE p.[Pozycja] = p2.[Pozycja]
) AS Calk_ciez,
(
    SELECT SUM(r.Ilosc_zrealizowana)
        FROM dbo.Product_Recznie r
    WHERE p.[Pozycja] = r.[Pozycja]
) AS ilosc_zrealizowana,
 (
    SELECT SUM(r.Dlugosc_zrealizowana)
from dbo.Product_Recznie r
    WHERE p.[Pozycja] = r.[Pozycja]
) AS dlugosc_zre,
MAX(r.Maszyna) AS maszyna,
MAX(r.Data) AS data,
p.[Profil] AS profil,
p.[Material] AS material,
p.Uwaga AS uwaga,
(
    SELECT DISTINCT [Osoba] + ','
    FROM dbo.Product_Recznie r2
    WHERE p.[Pozycja] = r2.[Pozycja]
    FOR XML PATH('')
) AS wykonal,
p.Uwaga AS uwaga
FROM
dbo.Parts p
LEFT JOIN
dbo.Product_Recznie r ON p.[Pozycja] = r.[Pozycja]
LEFT JOIN [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
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
p.[Projekt], p.[Pozycja], p.Ciezar, p.[Profil], p.[Material], p.Uwaga, p.[Status], p.Id_import, v.[AmountNeeded]";
$dataother = sqlsrv_query($conn, $sqlother);
?>