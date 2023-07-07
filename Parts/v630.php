<?php
require_once("dbconnect.php");


$sql = "Select Distinct 
p.[Projekt] as ProjectName
,STRING_AGG(p.[Zespol],' | ') as zespol
,p.[Pozycja] as Name
,Sum(p.[Ilosc]) as ilosc,
(
SELECT SUM(p2.[AmountDone])
FROM [PartCheck].[dbo].[Product_V630] p2
WHERE p2.[Name] = p.[Pozycja]
) AS AmountDone,
  'V630' as machine
,p.[Profil]
,p.[Material]
,p.[Dlugosc]
,v.[AmountNeeded],(
SELECT SUM(v1.[AmountDone])
FROM [PartCheck].[dbo].[Product_V620] v1
WHERE v1.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
) as completev620  
,b.[SawLength]
,p.[Ciezar]
,Sum(p.[Calk_ciez]) as Calk_ciez
,p.[Uwaga]
,Max(b.[ModificationDate]) as ModificationDate
from [PartCheck].[dbo].[Product_V630] as b INNER JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja]
INNER JOIN [PartCheck].[dbo].[Product_V620] as v ON v.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
group by p.[Pozycja],p.[Profil],p.[Material],p.[Dlugosc],p.[Ciezar],p.[Uwaga],b.[SawLength],p.[Projekt],v.[AmountNeeded]";
$datas = sqlsrv_query($conn, $sql); 

?>