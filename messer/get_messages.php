<?php


require_once('dbconnect.php');

$sql = "SELECT massage, osoba, [date] as time FROM PartCheck.dbo.messages_messer where CONVERT(DATE, [date]) >= CONVERT(DATE, GETDATE()-6) ORDER BY [date] desc";

// Wykonaj zapytanie SQL
$result = sqlsrv_query($conn, $sql);

// Sprawdź, czy zapytanie się powiodło
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Utwórz tablicę na wiadomości
$messages = array();

// Przetwórz wyniki zapytania
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    // Dodaj wiadomość do tablicy
    $messages[] = $row;
}

// Zwolnij zasoby zapytania
sqlsrv_free_stmt($result);

// Zamknij połączenie z bazą danych
sqlsrv_close($conn);

// Zwróć wiadomości w formie JSON
echo json_encode($messages);

?>