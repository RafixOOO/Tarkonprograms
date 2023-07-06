<?php

require_once("dbconnect.php");

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