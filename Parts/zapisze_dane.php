<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projekt = $_POST["project"];
    $detal = $_POST["detal"];
    $ilosc = $_POST["ilosc"];
    $dlugosc = $_POST["dlugosc"];
    $maszyna = $_POST["maszyna"];
    $wykonawca = $_POST["numer"];
    $status;
    try{
        $save = $_POST['save'];
    }catch(Exception $e){
    }
    
    if ($save === 'piece') {
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
        
        require_once("dbconnect.php");
        try{
            
            $sql = "Select [Id_import] as import from dbo.Parts where Projekt='$projekt' and Pozycja='$detal'";
        $resultdelete = sqlsrv_query($conn, $sql);
        if ($resultdelete === false) {
            throw new Exception("Błąd wykonania zapytania SQL.".sqlsrv_errors());
        }

        while ($row = sqlsrv_fetch_array($resultdelete, SQLSRV_FETCH_ASSOC)) {
          $import=$row['import'];
        }

        $sqldelete = "DELETE FROM [dbo].[Parts]
        WHERE Id_import=$import";
        sqlsrv_query($conn, $sqldelete);

        header('Location: main.php');

        }catch (Exception $e) {
    // Obsługa wyjątku
    echo "Wystąpił błąd: " . $e->getMessage();
}
        
    }
}
?>
