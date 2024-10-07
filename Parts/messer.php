<?php

require_once("dbconnect.php");
require_once '../auth.php';





$sqlmesser = "SELECT '' as wykonal,p.Id_import as import, '' as dlugosc,'Messer' as maszyna,'' as dlugosc_zre,'' as Ciezar,'' as Calk_ciez,'' as uwaga, p.[Status] as status, m.Projekt as ProjectName,p.lock as lok,m.program as cutlogic,v.[AmountNeeded] as ilosc_v200,m.amount as amount_order,
(
    SELECT SUM(v1.[AmountDone])
    FROM [PartCheck].[dbo].[Product_V200] v1
    WHERE v1.[Name]=m.PartName COLLATE Latin1_General_CS_AS
) as ilosc_v200_zre ,m.[PartName] as Detal,(SELECT STRING_AGG(CONCAT(p2.[Zespol], '(', p3.[Ilosc],'*',p2.[Ilosc]/p3.[Ilosc],') '), ' | ')
FROM [PartCheck].[dbo].[Parts] p2
left JOIN [PartCheck].[dbo].[Parts] p3 ON p2.[Zespol] = p3.[Zespol] and p3.[Pozycja] = ''
WHERE m.PartName = p2.[Pozycja] COLLATE Latin1_General_CS_AS
) AS zespol,m.grubosc as profil,(SELECT SUM(p1.Ilosc)
    FROM [PartCheck].[dbo].[Parts] p1
	where p1.Pozycja=m.PartName COLLATE Latin1_General_CS_AS) as ilosc,m.Complet as ilosc_zrealizowana,m.material as material,m.DataWykonania as data
from (SELECT 
ISNULL(p.[WoNumber], m1.[WoNumber]) AS Projekt,
ISNULL(p.[PartName], m1.[PartName]) as PartName,
ISNULL(p.[Thickness], m1.[Thickness]) AS grubosc,
ISNULL(p.[QtyOrdered], m1.[QtyOrdered]) as amount,
ISNULL(p.[QtyCompleted], m1.[QtyProgram]) AS Complet,
ISNULL(p.[Material], m1.[Material]) AS material,
ISNULL(STUFF((
    SELECT DISTINCT  ' ' + ProgramName + ' / ' 
    FROM [PartCheck].[dbo].[PartArchive_Messer] AS sub
    WHERE sub.ProgramName IS NOT NULL AND (sub.[PartName] = m1.[PartName] or sub.[PartName] = p.[PartName])
    FOR XML PATH('')), 1, 1, ''), STUFF((
    SELECT DISTINCT  ' ' + ProgramName + ' / ' 
    FROM [SNDBASE_PROD].[dbo].[PIP] AS sub
    WHERE sub.ProgramName IS NOT NULL AND (sub.[PartName] = m1.[PartName] or sub.[PartName] = p.[PartName])
    FOR XML PATH('')), 1, 1, '')) AS program,
MAX(m1.[ArcDateTime]) AS DataWykonania
FROM [PartCheck].[dbo].[PartArchive_Messer] m1
right join [SNDBASE_PROD].[dbo].[Part] p on m1.PartName=p.PartName
GROUP BY m1.[WoNumber], m1.[PartName], m1.[Thickness], m1.[Material], p.[WoNumber],p.[PartName],p.[Thickness],p.[QtyOrdered],p.[QtyCompleted],p.[Material], p.[QtyOrdered],m1.[QtyOrdered],p.[QtyCompleted], m1.[QtyProgram]) as m 
left JOIN [PartCheck].[dbo].[Parts] as p ON p.Pozycja=m.PartName COLLATE Latin1_General_CS_AS
left Join [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=m.PartName COLLATE Latin1_General_CS_AS
where m.Projekt='$_SESSION[project_name]'
GROUP BY m.Projekt,m.[PartName],m.grubosc,m.Complet,m.material,m.DataWykonania, m.PartName, p.[Status], p.Id_import,p.lock,v.[AmountNeeded],m.program,m.amount";
$data = sqlsrv_query($conn, $sqlmesser);
