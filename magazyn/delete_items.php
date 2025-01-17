<?php
// Sprawdź, czy żądanie jest typu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Połącz się z bazą danych
    require_once("../dbconnect.php");


    // Pobierz dane przesłane za pomocą POST
    $partId = $_POST['partId'];
    $localization = $_POST['localization'];
    $quantityToRemove = $_POST['quantityToRemove'];
    $transport = isset($_POST['transport']) ? $_POST['transport'] : '0'; // Pobierz stan checkboxa transport

    // Jeżeli transport jest zaznaczony, zmieniamy lokalizację na 18
    if ($transport === '1') {
        $newlocalization = 18;
    }else{
        $newlocalization = $localization;
    }

    // Wykonaj zapytanie SQL tyle razy, ile użytkownik wybrał produktów do usunięcia
    for ($i = 0; $i < $quantityToRemove; $i++) {
        // Twórz zapytanie SQL
        $sql = "UPDATE MagazynExtra
                SET Deleted = 1, Localization = ?
                WHERE PartID = ? AND Localization = ?
                AND Date = (SELECT MIN(Date) FROM MagazynExtra WHERE PartID = ? AND Localization = ? AND Deleted = 0)";

        // Przygotuj i wykonaj zapytanie
        $params = array($newlocalization, $partId, $localization, $partId, $localization);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    // Zamknij połączenie z bazą danych
    sqlsrv_close($conn);
}
?>
