<?php

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
            $data['ProfileId'],
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


?>