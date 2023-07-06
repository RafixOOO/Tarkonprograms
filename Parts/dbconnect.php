<?php

$serverName = '10.100.100.48,49827';
$connectionOptions = array(
    "Database" => "PartCheck",
    "Uid" => "Sa",
    "PWD" => "Shark1445NE\$T"
);



$conn = sqlsrv_connect($serverName, $connectionOptions);



if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}




?>