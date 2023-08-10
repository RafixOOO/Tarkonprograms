<?php
require_once('../auth.php');
require_once('dbconnect.php');
$allData = $_POST['allData'];
$i = "0A";
foreach ($allData as $key => $value) {
    $sql = "UPDATE [SNDBASE_PROD].[dbo].[Program]
    SET [Comment]=Concat('$i,',PARSENAME(REPLACE([Comment], ',', '.'), 1))
    where [ArchivePacketID]=$value and [Comment] LIKE '[0-9]%';";
    sqlsrv_query($conn, $sql);
    $i++;
}
logUserActivity($_SESSION['imie_nazwisko'],'Sortowanie w aplikacji messer');