<?php
// Sprawdź, czy żądanie jest typu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Połącz się z bazą danych
    require_once("../dbconnect.php");
    require_once '../auth.php';

    // Pobierz dane przesłane za pomocą POST
    $partId = $_POST['partId'];
    $localization = (int)$_POST['localization']; // Zamiana na int
    $quantityToRemove = (int)$_POST['quantityToRemove']; // Zamiana na int

    // Sprawdzenie, czy dane są prawidłowe
    if (!isset($partId) || !isset($localization) || !isset($quantityToRemove) || !is_numeric($quantityToRemove) || $quantityToRemove <= 0) {
        echo "Błąd: niewłaściwe dane wejściowe.".$partId." ". $localization . " " .$quantityToRemove;
        exit;
    }

    // Wykonaj zapytanie SQL tyle razy, ile użytkownik wybrał produktów do usunięcia
    for ($i = 0; $i < $quantityToRemove; $i++) {
        // Twórz zapytanie SQL
        $sql = "INSERT INTO PartCheck.dbo.MagazynExtra
(PartID, Person, Localization)
VALUES(?, ?, ?);";

        // Przygotuj i wykonaj zapytanie
        $params = array($partId, $_SESSION['imie_nazwisko'], $localization);
        $stmt = sqlsrv_query($conn, $sql, $params);

        // Sprawdź, czy zapytanie się powiodło
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    // Zamknij połączenie z bazą danych
    sqlsrv_close($conn);

    // Zwróć sukces
    echo "Produkty zostały dodane do bazy.";
}
?>
