<?php

require_once("dbconnect.php");





$sqlmesser = "SELECT '' as wykonal,p.Id_import as import, '' as dlugosc,'' as dlugosc_zre,'' as Ciezar,'' as Calk_ciez,'' as uwaga, p.[Status] as status, p.Projekt as ProjectName,
(SELECT SUM(v1.[AmountNeeded])
    FROM [PartCheck].[dbo].[Product_V200] v1
    WHERE v1.[Name]=m.PartName COLLATE Latin1_General_CS_AS
) as ilosc_v200
,(
    SELECT SUM(v1.[AmountDone])
    FROM [PartCheck].[dbo].[Product_V200] v1
    WHERE v1.[Name]=m.PartName COLLATE Latin1_General_CS_AS
) as ilosc_v200_zre ,p.[Pozycja] as Detal,(SELECT STRING_AGG(CONCAT(p2.[Zespol], '(', p3.[Ilosc],'*',p2.[Ilosc]/p3.[Ilosc],') '), ' | ')
FROM [PartCheck].[dbo].[Parts] p2
LEFT JOIN [PartCheck].[dbo].[Parts] p3 ON p2.[Zespol] = p3.[Zespol] and p3.[Pozycja] = ''
WHERE m.PartName = p2.[Pozycja] COLLATE Latin1_General_CS_AS
) AS zespol,m.grubosc as profil,(SELECT SUM(p1.Ilosc)
    FROM [PartCheck].[dbo].[Parts] p1
	where p1.Pozycja=m.PartName COLLATE Latin1_General_CS_AS) as ilosc,m.Complet as ilosc_zrealizowana,m.machine as maszyna,m.material as material,m.DataWykonania as data
from (SELECT 
[WoNumber] AS Projekt,
[PartName],
[Thickness] AS grubosc,
[QtyOrdered] AS zapotrzebowanie,
SUM([QtyProgram]) AS Complet,
'Messer' AS machine,
[Material] AS material,
STUFF((
    SELECT DISTINCT '/' + ProgramName
    FROM [PartCheck].[dbo].[PartArchive_Messer] AS sub
    WHERE sub.ProgramName IS NOT NULL AND sub.[WoNumber] = [PartArchive_Messer].[WoNumber]
    FOR XML PATH('')), 1, 1, '') AS program,
MAX([ArcDateTime]) AS DataWykonania
FROM [PartCheck].[dbo].[PartArchive_Messer]
GROUP BY [WoNumber], [PartName], [Thickness], [QtyOrdered], [Material]) as m 
Inner JOIN [PartCheck].[dbo].[Parts] as p ON p.Pozycja=m.PartName COLLATE Latin1_General_CS_AS
LEFT Join [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=m.PartName COLLATE Latin1_General_CS_AS
GROUP BY p.Projekt,p.[Pozycja],m.grubosc,m.Complet,m.machine,m.material,m.DataWykonania, m.PartName, p.[Status], p.Id_import";
$datasmesser = sqlsrv_query($conn, $sqlmesser); 

?>