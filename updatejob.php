<?php
require_once("dbconnect.php");

$servername = "10.100.100.27";
$username = "root";
$password = "kHLqB9AN-liWJUODhycsCYIPdUw";
$dbname = "mdc";

// Tworzenie połączenia
$conn1 = new mysqli($servername, $username, $password, $dbname);

// Sprawdzanie połączenia
if ($conn1->connect_error) {
    die("Connection failed: " . $conn1->connect_error);
}

$sql = "SELECT id, `_internal_timestamp`, machine_id, msg, `_internal_endtime`
FROM mdc.db_jobstable where msg IS NOT NULL";

$result = $conn1->query($sql);

// Sprawdź czy udało się wykonać zapytanie
if (!$result) {
    die('Błąd wykonania zapytania: ' . $conn1->error);
}

// Przetwarzaj wyniki zapytania
$messerresult = [];
while ($row = $result->fetch_assoc()) {
    $row['_internal_timestamp'] = (new DateTime($row['_internal_timestamp']))->format('Y-m-d H:i:s');
    $row['_internal_endtime'] = (new DateTime($row['_internal_endtime']))->format('Y-m-d H:i:s');
    $messerresult[] = $row;
}

$sqlServerQuery = "SELECT id, [_internal_timestamp], machine_id, msg, [_internal_endtime]
FROM PartCheck.dbo.Jobtable;";
$sqlServerResult = sqlsrv_query($conn, $sqlServerQuery);
if ($sqlServerResult === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Pobierz wyniki z SQL Server
$sqlServerResults1 = [];
while ($row = sqlsrv_fetch_array($sqlServerResult, SQLSRV_FETCH_ASSOC)) {
    $sqlServerResults1[] = $row;
}

$differences = [];
foreach ($messerresult as $messerdRow) {
    $found = false;
    foreach ($sqlServerResults1 as $sqlServerRow) {
        if ($messerdRow['id'] == $sqlServerRow['id']) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        // Dodaj różnice do tablicy
        $differences[] = $messerdRow;
    }
}

// Wstaw różnice do bazy danych SQL Server
foreach ($differences as $difference) {
    $sqlInsertQuery = "INSERT INTO PartCheck.dbo.Jobtable (id, [_internal_timestamp], machine_id, msg, [_internal_endtime]) 
                       VALUES (?, ?, ?, ?, ?)";
    $params = array(
        $difference['id'], $difference['_internal_timestamp'], $difference['machine_id'], $difference['msg'], 
        $difference['_internal_endtime']
    );

    $stmt = sqlsrv_query($conn, $sqlInsertQuery, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
}

// Zamknij połączenia
$conn1->close();

echo 'Różnice zostały dodane do bazy danych SQL Server.';