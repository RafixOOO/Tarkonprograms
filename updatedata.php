<?php 


header("refresh: 60;");


require_once('dbconnect.php');

try {
    if ($conn) {
        $sqlcheck = "SELECT [Id], [ProjectName], [Name], [AssignmentNumber], [Material], [AmountNeeded], [AmountDone] FROM [serwer_v630].[VACAM].[dbo].[Product]
        EXCEPT
        SELECT [Id], [ProjectName], [Name], [AssignmentNumber], [Material], [AmountNeeded], [AmountDone] FROM [PartCheck].[dbo].[Product_V620]";

        $datas1 = sqlsrv_query($conn, $sqlcheck);

        if ($datas1 !== false) {

while ($data = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
    $sqlcheck2 = "SELECT * FROM [PartCheck].[dbo].[Product_V620] WHERE Id = ?";
    $params = array($data['Id']);
    $result = sqlsrv_query($conn, $sqlcheck2, $params);

    if (sqlsrv_has_rows($result)) {
        $update = "
            UPDATE [PartCheck].[dbo].[Product_V620]
            SET [Id] = ?
            , [ProjectName]= ?
            , [Name]= ?
            , [AssignmentNumber]= ?
            , [Material]= ?
            , [AmountNeeded]= ?
            , [AmountDone]= ?
            WHERE Id = ?
        ";
        $params = array(
            $data['Id'],
            $data['ProjectName'],
            $data['Name'],
            $data['AssignmentNumber'],
            $data['Material'],
            $data['AmountNeeded'],
            $data['AmountDone']
        );

        $stmt = sqlsrv_prepare($conn, $update, $params);

        if (sqlsrv_execute($stmt) === false) {
            echo "Error updating data: " . print_r(sqlsrv_errors(), true);
        } else {
            continue;
        }
    } else {
        $insert = "
        INSERT INTO [dbo].[Product_V620]
           ([Id], [ProjectName], [Name], [AssignmentNumber], [Material], [AmountNeeded], [AmountDone])
           VALUES
           (?,?,?,?,?,?,?)
        ";

        $params = array(
            $data['Id'],
            $data['ProjectName'],
            $data['Name'],
            $data['AssignmentNumber'],
            $data['Material'],
            $data['AmountNeeded'],
            $data['AmountDone'],


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

try {
    if ($conn) {
        $sqlcheck = "SELECT * FROM [serwer_v200].[VACAM].[dbo].[Product]
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
           (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
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


try {
    if ($conn) {
        $sqlcheck1 = "SELECT * FROM (SELECT 
        [AutoID],
        [WoNumber],
        [PartName],
        [Thickness],
        [QtyOrdered],
        [QtyProgram],
        [Material],
        [ProgramName],
        [ArcDateTime]
        FROM SNDBASE_PROD.[dbo].[PartArchive]) as a
                             EXCEPT
                             SELECT * FROM [PartCheck].[dbo].[PartArchive_Messer]";

        $datas2 = sqlsrv_query($conn, $sqlcheck1);

        if ($datas2 !== false) {

            while ($data = sqlsrv_fetch_array($datas2, SQLSRV_FETCH_ASSOC)) {
                $sqlcheck2 = "SELECT * FROM [PartCheck].[dbo].[PartArchive_Messer] WHERE AutoID = ?";
                $params = array($data['AutoID']);
                $result = sqlsrv_query($conn, $sqlcheck2, $params);

                if ($result !== false && sqlsrv_has_rows($result)) {
                    $update = "
                        UPDATE [PartCheck].[dbo].[PartArchive_Messer]
                        SET 
                        [WoNumber] = ?,
                        [ArcDateTime] = ?,
                        [PartName] = ?,
                        [Thickness] = ?,
                        [QtyOrdered] = ?,
                        [QtyProgram] = ?,
                        [Material] = ?,
                        [ProgramName] = ?,
                        WHERE AutoID = ?
                    ";

                    $params = array(
                        
                        $data['WoNumber'],
                        substr($data['ArcDateTime']->format('Y-m-d H:i:s.u'), 0, 23),
                        $data['PartName'],
                        $data['Thickness'],
                        $data['QtyOrdered'],
                        $data['QtyProgram'],
                        $data['Material'],
                        $data['ProgramName'],
                        $data['AutoID']
                    );

                    $stmt = sqlsrv_query($conn, $update, $params);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                } else {
                    $insert = "
                    SET IDENTITY_INSERT [PartCheck].[dbo].[PartArchive_Messer] ON;
                        INSERT INTO [PartCheck].[dbo].[PartArchive_Messer] (
                            [AutoID],
                        [WoNumber],
                        [ArcDateTime],
                        [PartName],
                        [Thickness],
                        [QtyOrdered],
                        [QtyProgram],
                        [Material],
                        [ProgramName]
                        ) VALUES (
                            ?,?,?,?,?,?,?,?,?
                        )
                        SET IDENTITY_INSERT [PartCheck].[dbo].[PartArchive_Messer] OFF;";

                    $params = array(
                        $data['AutoID'],
                        $data['WoNumber'],
                        substr($data['ArcDateTime']->format('Y-m-d H:i:s.u'), 0, 23),
                        $data['PartName'],
                        $data['Thickness'],
                        $data['QtyOrdered'],
                        $data['QtyProgram'],
                        $data['Material'],
                        $data['ProgramName']
                    );

                    $stmt = sqlsrv_query($conn, $insert, $params);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$currentDateTime = date('Y-m-d H:i:s');

echo $currentDateTime." - Dane zaktualizowane";

?>