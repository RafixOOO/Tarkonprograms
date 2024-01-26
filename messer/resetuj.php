<?php
require_once('dbconnect.php');

// Sprawdź, czy przesłano zmienną rowId
if (isset($_POST['rowId'])) {
    // Przypisz wartość do zmiennej
    $rowId = $_POST['rowId'];

    // Wykonaj zapytanie SQL z wykorzystaniem zmiennej
    $sql = "UPDATE SNDBASE_PROD.dbo.Program SET Comment='' WHERE ArchivePacketID = '$rowId'";
    $datas = sqlsrv_query($conn, $sql);

    // Zwróć odpowiedź do przeglądarki
    echo "Zaktualizowano bazę danych dla wiersza o ID: $rowId";
} else {
    // W przypadku braku przesłanej zmiennej rowId
    echo "Błąd: Brak przesłanej zmiennej rowId";
}
?>
