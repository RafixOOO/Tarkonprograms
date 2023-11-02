<?php
require_once("dbconnect.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sprawdź, czy klucz "rowId" został przesłany
    if(isset($_POST["rowId"])) {
        // Pobierz wartość ID z przesłanych danych
        $rowId = $_POST["rowId"];

        $sql2 = "UPDATE [PartCheck].[dbo].[cutlogic]
        SET [checkpr]=1 where [ID]='$rowId'";
        $res1 = sqlsrv_query($conn, $sql2);
        echo "Zakończono ID: " . $rowId;
    } else {
        // Klucz "rowId" nie został przesłany
        echo "Błąd: Brak ID w przesłanych danych.";
    }
} else {
    // Nieprawidłowe żądanie
    echo "Błąd: Nieprawidłowe żądanie.";
}