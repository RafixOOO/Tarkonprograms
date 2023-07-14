<?php
require_once("dbconnect.php");


$sql = "SELECT Distinct 
p.Id_import as import
,'' as wykonal,
p.[Status] as status,
p.[Projekt] as ProjectName
,(SELECT STRING_AGG(CONCAT(p2.[Zespol], '(', p3.[Ilosc],'*',p2.[Ilosc]/p3.[Ilosc],') '), ' | ')
FROM [PartCheck].[dbo].[Parts] p2
LEFT JOIN [PartCheck].[dbo].[Parts] p3 ON p2.[Zespol] = p3.[Zespol] and p3.[Pozycja] = ''
WHERE b.[Name] = p2.[Pozycja]
) AS zespol
,p.[Pozycja] as Detal
,Sum(p.[Ilosc]) as ilosc,
(
SELECT SUM(p2.[AmountDone])
FROM [PartCheck].[dbo].[Product_V630] p2
WHERE p2.[Name] = p.[Pozycja]
) AS ilosc_zrealizowana,
  'V630' as maszyna
,p.[Profil] as profil
,p.[Material] as material
,p.[Dlugosc] as dlugosc
,v.[AmountNeeded] as ilosc_v200,(
SELECT SUM(v1.[AmountDone])
FROM [PartCheck].[dbo].[Product_V200] v1
WHERE v1.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
) as ilosc_v200_zre  
,b.[SawLength] as dlugosc_zre
,p.[Ciezar] as Ciezar
,Sum(p.[Calk_ciez]) as Calk_ciez
,p.[Uwaga] as uwaga
,Max(b.[ModificationDate]) as data
from [PartCheck].[dbo].[Product_V630] as b INNER JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja]
LEFT JOIN [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
group by p.[Pozycja],p.[Profil],p.[Material],p.[Dlugosc],p.[Ciezar],p.[Uwaga],b.[SawLength],p.[Projekt],v.[AmountNeeded], p.[Status], b.[Name], p.Id_import
";
$datas = sqlsrv_query($conn, $sql); 

?>