<?php
require_once('dbconnect.php');
require_once('auth.php');
    
    $person = isIdent();
    
    if(isSidebar()==1){
        $sql = "UPDATE dbo.Persons SET sidebar=0 WHERE Id=$person";
        $_SESSION['sidebar']=0;
    }else if(isSidebar()==0){
        $sql = "UPDATE dbo.Persons SET sidebar=1 WHERE Id=$person";
        $_SESSION['sidebar']=1;
    }
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        // Usunięcie hasła powiodło się
        // Możesz wyświetlić komunikat lub przekierować użytkownika na inną stronę
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            // Jeśli referer nie jest dostępny, przekieruj np. na stronę główną
            header("Location: index.php");
        }
        exit();
    } else {
        // Wystąpił błąd podczas usuwania hasła
        // Możesz wyświetlić odpowiedni komunikat błędu
        echo "Wystąpił błąd podczas usuwania hasła.";
    }

?>