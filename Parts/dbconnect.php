<?php

$serverName = '10.100.100.48,49827';
$connectionOptions = array(
    "Database" => "PartCheck",
    "Uid" => "Sa",
    "PWD" => "Shark1445NE\$T"
);

$serverName1 = '559807-001\CAM';
$connectionOptions1 = array(
    "Database" => "VACAM",
    "Uid" => "VACAM-User",
    "PWD" => "vacon02"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
$conn1 = sqlsrv_connect($serverName1, $connectionOptions1);


if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($conn1 === false) {
    die(print_r(sqlsrv_errors(), true));
}



?>