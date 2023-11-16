<?php
// fetch_events.php

// Odbierz dane od strony klienta (daty początkowa i końcowa jako parametry z URL-a)
$startDate = $_GET['start'];
$endDate = $_GET['end'];

// Przygotuj zapytanie SQL w zależności od wybranego groupId
require_once("dbconnect.php");

$sqlv = "SELECT p.[Name], CAST(p.ModificationDate AS DATE) AS ModificationDate
         FROM [PartCheck].dbo.Product_V630 p
         WHERE CAST(p.ModificationDate AS DATE) BETWEEN '$startDate' AND '$endDate'
         GROUP BY p.[Name], CAST(p.ModificationDate AS DATE);";

$stmtV630 = sqlsrv_query($conn, $sqlv);

if ($stmtV630 === false) {
    die(print_r(sqlsrv_errors(), true));
}

$events = array();

while ($row = sqlsrv_fetch_array($stmtV630, SQLSRV_FETCH_ASSOC)) {
    $events[] = array(
        "groupId" => "v630",
        "title" => $row["Name"],
        "start" => $row["ModificationDate"]->format('Y-m-d'),
        "color" => "#227525"
    );
}

$sqlm = "SELECT p.[ProgramName], CAST(p.ArcDateTime AS DATE) AS ModificationDate
         FROM [PartCheck].dbo.PartArchive_Messer p
         WHERE CAST(p.ArcDateTime AS DATE) BETWEEN '$startDate' AND '$endDate'
         GROUP BY p.[ProgramName], CAST(p.ArcDateTime AS DATE);";

$stmtMesser = sqlsrv_query($conn, $sqlm);

if ($stmtMesser === false) {
    die(print_r(sqlsrv_errors(), true));
}

while ($row1 = sqlsrv_fetch_array($stmtMesser, SQLSRV_FETCH_ASSOC)) {
    $events[] = array(
        "groupId" => "messer",
        "title" => $row1["ProgramName"],
        "start" => $row1["ModificationDate"]->format('Y-m-d'),
        "color" => "#1b1b63"
    );
}

$sqlr="SELECT
p.[Pozycja]
,CAST(p.Data AS DATE) AS ModificationDate
,p.[Maszyna]
FROM [PartCheck].[dbo].[Product_Recznie] p
group by p.[Pozycja],CAST(p.Data AS DATE),p.[Maszyna]";

$stmtRecznie = sqlsrv_query($conn, $sqlr);

if ($stmtRecznie === false) {
    die(print_r(sqlsrv_errors(), true));
}

while ($row2 = sqlsrv_fetch_array($stmtRecznie, SQLSRV_FETCH_ASSOC)) {
    $events[] = array(
        "groupId" => $row2["Maszyna"],
        "title" => $row2["Pozycja"],
        "start" => $row2["ModificationDate"]->format('Y-m-d'),
        "color" => "#C13A1D"
    );
}

// Zwróć dane jako odpowiedź w formacie JSON
header('Content-Type: application/json');
echo json_encode($events);

// Zakończ połączenie z bazą danych
sqlsrv_free_stmt($stmtV630);
sqlsrv_free_stmt($stmtMesser);
sqlsrv_close($conn);
?>
