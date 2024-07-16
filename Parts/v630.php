<?php
require_once("dbconnect.php");
require_once '../auth.php'; 


$sql = "SELECT Distinct 
p.Id_import as import
,'' as cutlogic
,p.lock as lok
,'' as wykonal,
p.[Status] as status,
b.[ProjectName] as ProjectName
,(SELECT STRING_AGG(CONCAT(p2.[Zespol], '(', p3.[Ilosc],'*',p2.[Ilosc]/p3.[Ilosc],') '), ' | ')
FROM [PartCheck].[dbo].[Parts] p2
LEFT JOIN [PartCheck].[dbo].[Parts] p3 ON p2.[Zespol] = p3.[Zespol] and p3.[Pozycja] = ''
WHERE b.[Name] = p2.[Pozycja]
) AS zespol
,b.[Name] as Detal
,(select sum(p1.[Ilosc])
from [PartCheck].[dbo].[Parts] p1
where p1.[Pozycja]=b.[Name]) as ilosc,
(SELECT SUM(p3.[AmountNeeded])
FROM [PartCheck].[dbo].[Product_V630] p3
WHERE p3.[Name] = b.[Name]
) AS amount_order,
(SELECT SUM(p2.[AmountDone])
FROM [PartCheck].[dbo].[Product_V630] p2
WHERE p2.[Name] = b.[Name]
) AS ilosc_zrealizowana,
  'V630' as maszyna
,p.[Profil] as profil
,p.[Material] as material
,Max(p.[Dlugosc]) as dlugosc
,v.[AmountNeeded] as ilosc_v200,(
SELECT SUM(v1.[AmountDone])
FROM [PartCheck].[dbo].[Product_V200] v1
WHERE v1.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
) as ilosc_v200_zre  
,b.[SawLength] as dlugosc_zre
,Max(p.[Ciezar]) as Ciezar
,Sum(p.[Calk_ciez]) as Calk_ciez
,p.[Uwaga] as uwaga
,Max(b.[ModificationDate]) as data
from [PartCheck].[dbo].[Product_V630] as b left JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja]
LEFT JOIN [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
where b.[ProjectName]='$_SESSION[project_name]'
group by p.[Pozycja],p.[Profil],p.[Material],p.[Uwaga],b.[SawLength],b.[ProjectName],v.[AmountNeeded], p.[Status], b.[Name], p.Id_import,p.lock
";
$data = sqlsrv_query($conn, $sql); 

?>