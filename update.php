<?php
require_once('auth.php');
requireLogin();

require_once('dbconnect.php');
$myField = $_POST['myField'];
$id = $_POST['id'];
$com = $_POST['lop'];

$let = substr($com, 0, 3);
$sql1 = "UPDATE [SNDBASE_PROD].[dbo].[Program]
        SET [Comment]='$let$myField'
        where [ArchivePacketID]=$id";
sqlsrv_query($conn, $sql1);

sqlsrv_close($conn);
header('Location: index.php');
exit();
?>