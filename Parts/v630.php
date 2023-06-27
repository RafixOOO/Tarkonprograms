<?php
require_once("dbconnect.php");



try {
    if ($conn) {
        $sqlcheck = "SELECT * FROM [VACAM].[dbo].[Product]
        EXCEPT
        SELECT * FROM [PartCheck].[dbo].[Product_V630]";

        $datas1 = sqlsrv_query($conn, $sqlcheck);

        if ($datas1 !== false) {

while ($data = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
    $sqlcheck2 = "SELECT * FROM [PartCheck].[dbo].[Product_V630] WHERE Id = ?";
    $params = array($data['Id']);
    $result = sqlsrv_query($conn, $sqlcheck2, $params);

    if (sqlsrv_has_rows($result)) {
        $update = "
            UPDATE [PartCheck].[dbo].[Product_V630]
            SET [ProjectName] = ?
                ,[PhaseName] = ?
                ,[Name] = ?
                ,[ProfileId] = ?
                ,[AssignmentNumber] = ?
                ,[DrawingNumber] = ?
                ,[PartNumber] = ?
                ,[PositionNumber] = ?
                ,[Material] = ?
                ,[StartView] = ?
                ,[AmountNeeded] = ?
                ,[AmountDone] = ?
                ,[Length] = ?
                ,[SawLength] = ?
                ,[TextLine1] = ?
                ,[TextLine2] = ?
                ,[TextLine3] = ?
                ,[TextLine4] = ?
                ,[Rotation] = ?
                ,[Note] = ?
                ,[ProductStatus] = ?
                ,[Selection] = ?
                ,[RecordStatus] = ?
                ,[Blast] = ?
                ,[PostProcessing] = ?
                ,[ModificationDate] = ?
                ,[StandardProduct] = ?
                ,[BatchRotation] = ?
                ,[UnsolvedProductId] = ?
                ,[SolvedRotation] = ?
                ,[MachineGroupId] = ?
                ,[DivisorId] = ?
                ,[AutoProduct] = ?
                ,[NestedProduct] = ?
            WHERE Id = ?
        ";
        $params = array(
            $data['ProjectName'],
            $data['PhaseName'],
            $data['Name'],
            $data['ProfileId'],
            $data['AssignmentNumber'],
            $data['DrawingNumber'],
            $data['PartNumber'],
            $data['PositionNumber'],
            $data['Material'],
            $data['StartView'],
            $data['AmountNeeded'],
            $data['AmountDone'],
            $data['Length'],
            $data['SawLength'],
            $data['TextLine1'],
            $data['TextLine2'],
            $data['TextLine3'],
            $data['TextLine4'],
            $data['Rotation'],
            $data['Note'],
            $data['ProductStatus'],
            $data['Selection'],
            $data['RecordStatus'],
            $data['Blast'],
            $data['PostProcessing'],
            substr($data['ModificationDate']->format('Y-m-d H:i:s.u'),0,23),
            $data['StandardProduct'],
            $data['BatchRotation'],
            $data['UnsolvedProductId'],
            $data['SolvedRotation'],
            $data['MachineGroupId'],
            $data['DivisorId'],
            $data['AutoProduct'],
            $data['NestedProduct'],
            $data['Id']
        );

        $stmt = sqlsrv_prepare($conn, $update, $params);

        if (sqlsrv_execute($stmt) === false) {
            echo "Error updating data: " . print_r(sqlsrv_errors(), true);
        } else {
            continue;
        }
    } else {
        $insert = "
        INSERT INTO [dbo].[Product_V630]
           ([ProjectName]
           ,[PhaseName]
           ,[Name]
           ,[ProfileId]
           ,[AssignmentNumber]
           ,[DrawingNumber]
           ,[PartNumber]
           ,[PositionNumber]
           ,[Material]
           ,[StartView]
           ,[AmountNeeded]
           ,[AmountDone]
           ,[Length]
           ,[SawLength]
           ,[TextLine1]
           ,[TextLine2]
           ,[TextLine3]
           ,[TextLine4]
           ,[Rotation]
           ,[Note]
           ,[ProductStatus]
           ,[Selection]
           ,[RecordStatus]
           ,[Blast]
           ,[PostProcessing]
           ,[ModificationDate]
           ,[StandardProduct]
           ,[BatchRotation]
           ,[UnsolvedProductId]
           ,[SolvedRotation]
           ,[MachineGroupId]
           ,[DivisorId]
           ,[AutoProduct]
           ,[NestedProduct])
           VALUES
           (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ";

        $params = array(
            $data['ProjectName'],
            $data['PhaseName'],
            $data['Name'],
            $data['ProfileId'],
            $data['AssignmentNumber'],
            $data['DrawingNumber'],
            $data['PartNumber'],
            $data['PositionNumber'],
            $data['Material'],
            $data['StartView'],
            $data['AmountNeeded'],
            $data['AmountDone'],
            $data['Length'],
            $data['SawLength'],
            $data['TextLine1'],
            $data['TextLine2'],
            $data['TextLine3'],
            $data['TextLine4'],
            $data['Rotation'],
            $data['Note'],
            $data['ProductStatus'],
            $data['Selection'],
            $data['RecordStatus'],
            $data['Blast'],
            $data['PostProcessing'],
            substr($data['ModificationDate']->format('Y-m-d H:i:s.u'),0,23),
            $data['StandardProduct'],
            $data['BatchRotation'],
            $data['UnsolvedProductId'],
            $data['SolvedRotation'],
            $data['MachineGroupId'],
            $data['DivisorId'],
            $data['AutoProduct'],
            $data['NestedProduct']
        );

        $stmt = sqlsrv_prepare($conn, $insert, $params);

        if (sqlsrv_execute($stmt) === false) {
            echo "Error inserting data: " . print_r(sqlsrv_errors(), true);
        } else {
            continue;
        }
    }
}
} 
}
} catch (Exception $e) {
}


$sql = "Select Distinct 
    b.[ProjectName]
   ,STRING_AGG(p.[Zespol],' | ') as zespol
   ,b.[Name]
  ,Sum(p.[Ilosc]) as ilosc
  ,b.[AmountDone] 
  ,		'V630' as machine
  ,p.[Profil]
  ,p.[Material]
  ,p.[Dlugosc]
  ,b.[SawLength]
  ,p.[Ciezar]
  ,Sum(p.[Calk_ciez]) as Calk_ciez
  ,p.[Uwaga]
  ,Max(b.[ModificationDate]) as ModificationDate
   from [PartCheck].[dbo].[Product_V630] as b LEFT JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja] 
   group by b.[AmountDone],b.[Name],p.[Profil],p.[Material],p.[Dlugosc],p.[Ciezar],p.[Uwaga],b.[SawLength],b.[ProjectName]";
$datas = sqlsrv_query($conn, $sql); ?>