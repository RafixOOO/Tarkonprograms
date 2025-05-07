<?php 
require_once '../auth.php'; 
require_once("../dbconnect.php");

if (isset($_GET['rfid'])) {
    $rfid = $_GET['rfid'];
} else {
    echo "Brak numeru RFID.";
}

$sql = "UPDATE PartCheck.dbo.HrapWorkTime
        SET cuce_time_to=CONVERT([varchar](5),getdate(),(108)), cuce_date_to=CONVERT([date],getdate())
        WHERE PersonID = ? AND cuce_time_to IS NULL AND cuce_date_to IS NULL";

$params = array($rfid);
$stmt = sqlsrv_prepare($conn, $sql, $params);

if ($stmt) {
    sqlsrv_execute($stmt);
    $previousPage = $_SERVER['HTTP_REFERER'];

$parsedUrl = parse_url($previousPage);

// Rozbij query na tablicę
parse_str($parsedUrl['query'], $queryParams);

// Zmień parametr
$queryParams['finish'] = 2;

// Zbuduj nowy query string
$newQuery = http_build_query($queryParams);

// Złóż URL ponownie
$newUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . '?' . $newQuery;
    header("Location: $newUrl");
    exit();
} else {
    // Obsługa błędu przygotowania zapytania
    die(print_r(sqlsrv_errors(), true));
}