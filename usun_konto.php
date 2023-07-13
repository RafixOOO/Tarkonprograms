<?php
require_once('dbconnect.php');
require_once('auth.php');
if (isset($_POST['usun_konto'])) {
    $personId = $_POST['person_id'];

    // Tutaj dodaj kod do usunięcia konta osoby o podanym identyfikatorze ($personId) z bazy danych

    // Przykładowy kod, który usuwa konto
    $sql = "DELETE FROM dbo.Persons WHERE Id = $personId";
    $stmt = sqlsrv_query($conn, $sql);
    logUserActivity($_SESSION['imie_nazwisko'],'Usunięcię konta użytkownikowi o ID:'.$personId);
    if ($stmt) {
        // Usunięcie konta powiodło się
        // Możesz wyświetlić komunikat lub przekierować użytkownika na inną stronę
        header("Location: zarzadzaj.php");
        exit();
    } else {
        // Wystąpił błąd podczas usuwania konta
        // Możesz wyświetlić odpowiedni komunikat błędu
        echo "Wystąpił błąd podczas usuwania konta.";
    }
}
?>
