<?php
require_once('dbconnect.php');

if (isset($_POST['change_status'])) {
    $personId = $_POST['person_id'];
    $role = $_POST['role'];

    // Tutaj dodaj kod do zmiany statusu osoby o podanym identyfikatorze ($personId) i roli ($role) w bazie danych

    // Przykładowy kod, który aktualizuje status w bazie danych
    $sql = "UPDATE dbo.Persons SET $role = CASE WHEN $role = 1 THEN 0 ELSE 1 END WHERE Id = $personId";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt) {
        // Zmiana statusu powiodła się
        // Możesz wyświetlić komunikat lub przekierować użytkownika na inną stronę
        header("Location: zarzadzaj.php");
        exit();
    } else {
        // Wystąpił błąd podczas zmiany statusu
        // Możesz wyświetlić odpowiedni komunikat błędu
        echo "Wystąpił błąd podczas zmiany statusu.";
    }
}
?>
