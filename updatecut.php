<?php

require_once("dbconnect.php");

$dsn = "Logic"; // Nazwa utworzonego DSN
$username = 'SYSDBA'; // Nazwa użytkownika bazy danych Firebird
$password = 'masterkey'; // Hasło użytkownika bazy danych Firebird

// Nawiąż połączenie z bazą danych Firebird
$conn = odbc_connect($dsn, $username, $password);

// Sprawdź czy udało się nawiązać połączenie
if (!$conn) {
    die('Błąd połączenia z bazą danych: ' . odbc_errormsg());
}

$sql = "SELECT distinct p2.ID || p2.NUM || s.DES1 || s.LEN AS id, p.NAME AS program, p.DES1 AS projekt, s.DES1 AS opis, s.LEN*0.1 AS Dlugosc, sum(s2.LEN*0.1) AS Resztki, p2.DES1 AS czesc, p2.LEN*0.1 AS czdlu, p2.CNT 
FROM PLANS p 
LEFT JOIN SOUS s ON p.ID = s.PLANID
LEFT JOIN SOUS s2 ON p.ID = s2.CRPLANID AND s.DES1 = s2.DES1
LEFT JOIN LAYPARTS l ON s2.LAYID = l.LAYID
LEFT JOIN PARTS p2 ON l.PARTID = p2.ID
WHERE s.ID IS NOT NULL 
AND s.PLANID IS NOT NULL
AND p2.LEN IS NOT NULL
AND s.DES1 != ''
AND p2.DES1 != ''
GROUP BY p.NAME, p.DES1, s.DES1, s.LEN*0.1, p2.DES1, p2.LEN*0.1, p2.CNT, p2.ID, p2.NUM, s.LEN 
ORDER BY p2.ID DESC";

$result = odbc_exec($conn, $sql);

// Sprawdź czy udało się wykonać zapytanie
if (!$result) {
    die('Błąd wykonania zapytania: ' . odbc_errormsg());
}

// Przetwarzaj wyniki zapytania
$firebirdResults = [];
while ($row = odbc_fetch_array($result)) {
    $firebirdResults[] = $row;
}

$sqlServerQuery = "SELECT ID, PROGRAM, PROJEKT, OPIS, DLUGOSC, RESZTKI, CZESC, CZDLU, CNT FROM PartCheck.dbo.cutlogic";
$sqlServerResult = sqlsrv_query($conn1, $sqlServerQuery);
if ($sqlServerResult === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Pobierz wyniki z SQL Server
$sqlServerResults = [];
while ($row = sqlsrv_fetch_array($sqlServerResult, SQLSRV_FETCH_ASSOC)) {
    $sqlServerResults[] = $row;
}

$differences = [];
foreach ($firebirdResults as $firebirdRow) {
    $found = false;
    foreach ($sqlServerResults as $sqlServerRow) {
        if ($firebirdRow['ID'] === $sqlServerRow['ID']) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        // Dodaj różnice do tablicy
        $differences[] = $firebirdRow;
    }
}

// Wstaw różnice do bazy danych SQL Server
foreach ($differences as $difference) {
    $sqlInsertQuery = "INSERT INTO PartCheck.dbo.cutlogic (ID, PROGRAM, PROJEKT, OPIS, DLUGOSC, RESZTKI, CZESC, CZDLU, CNT) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = array(
        $difference['ID'], $difference['PROGRAM'], $difference['PROJEKT'], $difference['OPIS'], 
        $difference['DLUGOSC'], $difference['RESZTKI'], $difference['CZESC'], $difference['CZDLU'], $difference['CNT']
    );

    $stmt = sqlsrv_query($conn1, $sqlInsertQuery, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
}

// Zamknij połączenia
odbc_close($conn);
sqlsrv_close($conn1);

echo 'Różnice zostały dodane do bazy danych SQL Server.';
?>