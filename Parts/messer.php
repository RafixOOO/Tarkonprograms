<?php
require_once("dbconnect.php");
$sqlmesser = "SELECT  [WONumber] as Projekt
,[PartName]
,[Thickness] as grubosc
      ,[QtyOrdered] as zapotrzebowanie
,[QtyCompleted] as Complet
,[QtyInProcess] as w_procesie
,'Messer' as machine
,[DrawingNumber]
,[Material] as material
,[DueDate]
,B.ProgramName as program
 ,B.QtyProgram as wykonane
   ,B.DataWykonania

FROM [PartCheck].[dbo].[PartWithQtyInProcess_Messer] as A
OUTER APPLY (SELECT ArcDateTime as DataWykonania, QtyProgram ,ProgramName FROM [PartCheck].dbo.PartArchive_Messer where PartName=A.PartName AND WoNumber=A.WONumber) as B";
$datasmesser = sqlsrv_query($conn, $sqlmesser); ?>