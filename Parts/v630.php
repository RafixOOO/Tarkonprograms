<?php
require_once("dbconnect.php");
$sql = "Select Distinct 
    b.[ProjectName]
   ,STRING_AGG(p.[Zespol],'/') as zespol
   ,b.[Name]
  ,Sum(p.[Ilosc]) as ilosc
  ,b.[AmountDone] 
  ,		'V630' as machine
  ,p.[Profil]
  ,p.[Material]
  ,p.[Dlugosc]
  ,b.[SawLength]
  ,p.[Ciezar]
  ,p.[Calk_ciez]
  ,p.[Uwaga]
  ,b.[ModificationDate]
   from [PartCheck].[dbo].[Product_V630] as b LEFT JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja] 
   group by b.[AmountDone],b.[Name],p.[Profil],p.[Material],p.[Dlugosc],p.[Ciezar],p.[Calk_ciez],p.[Uwaga],b.[ModificationDate],b.[SawLength],b.[ProjectName]";
$datas = sqlsrv_query($conn, $sql); ?>