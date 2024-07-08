<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../auth.php');
    try{
        $save = $_POST['save'];
    }catch(Exception $e){
    }
    
    if ($save === 'piece') {
      $projekt = $_POST["project"];
    $detal = $_POST["detal"];
    $ilosc = $_POST["ilosc"];
    $dlugosc = $_POST["dlugosc"];
    $maszyna = $_POST["maszyna"];
    $wykonawca = $_POST["numer"];
    $status;
      require_once("dbconnect.php");
      $osoba;
      $sql = "SELECT imie_nazwisko FROM dbo.Persons WHERE identyfikator = $wykonawca";
      $result = sqlsrv_query($conn, $sql);
      while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $osoba=$row['imie_nazwisko'];
    }

        
        $sqlinsert = "INSERT INTO dbo.Product_Recznie (Projekt, Pozycja, Ilosc_zrealizowana, Dlugosc_zrealizowana, Maszyna, Osoba) VALUES ('{$projekt}', '{$detal}', '{$ilosc}', '{$dlugosc}', '{$maszyna}', '{$osoba}')";

        sqlsrv_query($conn, $sqlinsert);

        header('Location: main.php');

    }  else {

      $import=$_POST["import"];
        
        require_once("dbconnect.php");
        try{

        $sqldelete = "DELETE FROM [dbo].[Parts]
        WHERE Id_import=$import";
        sqlsrv_query($conn, $sqldelete);
        if($_SESSION['imie_nazwisko']==""){
            logUserActivity($wykonawca,'Zaktualizował aplikację parts: '.$import);
          }else{
            logUserActivity($_SESSION['imie_nazwisko'],'Zaktualizował aplikację parts: '.$import);
          }
        header('Location: main.php');

        }catch (Exception $e) {
    // Obsługa wyjątku
    echo "Wystąpił błąd: " . $e->getMessage();
}
        
    }
}
?>
