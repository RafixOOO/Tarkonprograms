<?php

$dsn = "odbc:Sap"; // Nazwa źródła danych ODBC
$user = "";
$password = "";

try {
    // Tworzenie połączenia PDO
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

?>