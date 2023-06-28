<?php
require_once('C:\xampp\htdocs\programs\Tarkonprograms\auth.php');

require_once('dbconnect.php');
$myField = $_POST['myField'];
$id = $_POST['id'];

$sql2 = "SELECT 
            [Comment] as zupa

                FROM [SNDBASE_PROD].[dbo].[Program]
                 where [ArchivePacketID]=$id";
$res1 = sqlsrv_query($conn, $sql2);
$max = "";
while ($row1 = sqlsrv_fetch_array($res1, SQLSRV_FETCH_ASSOC)) {
    $max = $row1["zupa"];
}

$let = substr($max, 0, 3);
$sql1 = "UPDATE [SNDBASE_PROD].[dbo].[Program]
        SET [Comment]='$let$myField'
        where [ArchivePacketID]=$id";
sqlsrv_query($conn, $sql1);

sqlsrv_close($conn);
header('Location: main.php');
exit();
?>