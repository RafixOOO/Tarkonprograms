<?php
require_once('dbconnect.php');
require_once('auth.php');
if (isset($_POST['change_status'])) {
    $personId = $_POST['person_id'];
    $role = $_POST['role'];
    

    
    $sql = "UPDATE dbo.Persons SET $role = CASE WHEN $role = 1 THEN 0 ELSE 1 END WHERE Id = $personId";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt) {
        logUserActivity($_SESSION['imie_nazwisko'],'Zmiana statusu użytkownikowi o ID:'.$personId);
        header("Location: zarzadzaj.php");
        exit();
    } else {
        
        echo "Wystąpił błąd podczas zmiany statusu.";
    }
}
?>
