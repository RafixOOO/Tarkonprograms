<?php

require_once("dbconnect.php");





$sqlmesser = "Select p.Projekt as Projekt,v.[AmountNeeded],(
    SELECT SUM(v1.[AmountDone])
    FROM [PartCheck].[dbo].[Product_V620] v1
    WHERE v1.[Name]=m.PartName COLLATE Latin1_General_CS_AS
) as completev620  ,p.[Pozycja] as PartName,STRING_AGG(p.[Zespol],' | ') as Zespol,m.grubosc as grubosc,(SELECT SUM(p1.Ilosc)
    FROM [PartCheck].[dbo].[Parts] p1
	where p1.Pozycja=m.PartName COLLATE Latin1_General_CS_AS) as zapotrzebowanie,m.Complet as Complet,m.machine as machine,m.material as material,m.DataWykonania as DataWykonania
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
Inner Join [PartCheck].[dbo].[Product_V620] as v ON v.[Name]=m.PartName COLLATE Latin1_General_CS_AS
GROUP BY p.Projekt,p.[Pozycja],m.grubosc,m.Complet,m.machine,m.material,m.DataWykonania,v.[AmountNeeded], m.PartName";
$datasmesser = sqlsrv_query($conn, $sqlmesser); 

?>