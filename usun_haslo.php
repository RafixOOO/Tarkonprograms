<?php
require_once('dbconnect.php');

if (isset($_POST['usun_haslo'])) {
    $personId = $_POST['person_id'];

    // Tutaj dodaj kod do usunięcia hasła osoby o podanym identyfikatorze ($personId) w bazie danych

    // Przykładowy kod, który usuwa hasło
    $sql = "UPDATE dbo.Persons SET password = NULL WHERE Id = $personId";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt) {
        // Usunięcie hasła powiodło się
        // Możesz wyświetlić komunikat lub przekierować użytkownika na inną stronę
        header("Location: zarzadzaj.php");
        exit();
    } else {
        // Wystąpił błąd podczas usuwania hasła
        // Możesz wyświetlić odpowiedni komunikat błędu
        echo "Wystąpił błąd podczas usuwania hasła.";
    }
}
?>
