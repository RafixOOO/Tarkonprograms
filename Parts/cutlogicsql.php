<?php
require_once("../dbconnect.php");
require_once '../auth.php'; 

$sqlcut = "SELECT
'' as amount_order,
p.Id_import as import
,v.[AmountNeeded] as ilosc_v200
,p.lock as lok
,c.[PROGRAM] as cutlogic
,(
SELECT SUM(v1.[AmountDone])
FROM [PartCheck].[dbo].[Product_V200] v1
WHERE v1.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
) as ilosc_v200_zre , 
c.[CZESC] AS Detal,
c.[PROJEKT] AS ProjectName,
p.[Status] as status,
(SELECT STRING_AGG(CONCAT(p2.[Zespol], '(', p3.[Ilosc],'*', CASE
    WHEN p3.[Ilosc] IS NOT NULL AND p3.[Ilosc] <> 0 THEN p2.[Ilosc]/p3.[Ilosc]
    ELSE p2.[Ilosc]
    END,') '), ' | ')
FROM [PartCheck].[dbo].[Parts] p2
LEFT JOIN [PartCheck].[dbo].[Parts] p3 ON p2.[Zespol] = p3.[Zespol] and p3.[Pozycja] = ''
WHERE p.[Pozycja] = p2.[Pozycja]
) AS zespol,
 (SELECT CASE 
        WHEN SUM(p2.Ilosc) IS NULL THEN c.CNT
        ELSE SUM(p2.Ilosc)
    END AS ilosc
    FROM [PartCheck].dbo.Parts p2
    WHERE c.[CZESC] = p2.[Pozycja]
) AS ilosc,
(
    SELECT CASE 
        WHEN 
            SUM(p2.Dlugosc) IS NULL THEN (c.CZDLU * c.CNT)
        ELSE SUM(p2.Dlugosc)
        
    END AS dlugosc
    FROM [PartCheck].dbo.Parts p2
    WHERE c.[CZESC] = p2.[Pozycja]
) AS dlugosc,
p.Ciezar AS Ciezar,(
    SELECT SUM(p2.Calk_ciez)
    FROM [PartCheck].dbo.Parts p2
    WHERE c.[CZESC] = p2.[Pozycja]
) AS Calk_ciez,
(
    SELECT SUM(r.Ilosc_zrealizowana)
        FROM [PartCheck].dbo.Product_Recznie r
    WHERE c.[CZESC] = r.[Pozycja]
) AS ilosc_zrealizowana,
 (
    SELECT SUM(r.Dlugosc_zrealizowana)
from [PartCheck].dbo.Product_Recznie r
    WHERE c.[CZESC] = r.[Pozycja]
) AS dlugosc_zre,
MAX(r.Maszyna) AS maszyna,
MAX(r.Data) AS data,
p.[Profil] AS profil,
p.[Material] AS material,
p.Uwaga AS uwaga,
(
    SELECT DISTINCT [Osoba] + ','
    FROM [PartCheck].dbo.Product_Recznie r2
    WHERE c.[CZESC] = r2.[Pozycja]
    FOR XML PATH('')
) AS wykonal,
p.Uwaga AS uwaga
FROM
[PartCheck].[dbo].[cutlogic] c
Left Join [PartCheck].dbo.Parts p on p.[Pozycja]=c.[CZESC]
LEFT JOIN
[PartCheck].dbo.Product_Recznie r ON c.[CZESC] = r.[Pozycja]
LEFT JOIN [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
WHERE
NOT EXISTS (
    SELECT 1
    FROM [PartCheck].dbo.PartArchive_Messer m
    WHERE p.Pozycja = m.PartName COLLATE Latin1_General_CS_AS
)
AND NOT EXISTS (
    SELECT 1
    FROM [PartCheck].dbo.Product_V630 v
    WHERE p.Pozycja = v.Name
)
AND c.[PROGRAM]!=''
and c.[CZESC]!=''
and c.[PROJEKT]='$_SESSION[project_name]'
GROUP BY
c.[PROJEKT], c.[CZESC], p.Ciezar, p.[Profil], p.[Material], p.Uwaga, p.[Status], p.Id_import, v.[AmountNeeded],p.lock,c.[PROGRAM],c.CNT,p.[Projekt], p.[Pozycja],c.DLUGOSC, c.RESZTKI,c.CZDLU
order by p.[Status] desc, p.Id_import desc, c.[PROGRAM] desc ";
$data = sqlsrv_query($conn, $sqlcut);
?>