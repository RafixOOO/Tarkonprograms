<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projekt = $_POST["project"];
    $detal = $_POST["detal"];
    $ilosc = $_POST["ilosc"];
    $dlugosc = $_POST["dlugosc"];
    $maszyna = $_POST["maszyna"];
    $status;
    try{
        $save = $_POST['save'];
    }catch(Exception $e){
    }
    
    if ($save === 'piece') {

        require_once("dbconnect.php");
        $sqlinsert = "INSERT INTO dbo.Product_Recznie (Projekt, Pozycja, Ilosc_zrealizowana, Dlugosc_zrealizowana, Maszyna) VALUES ('{$projekt}', '{$detal}', '{$ilosc}', '{$dlugosc}', '{$maszyna}')";

        sqlsrv_query($conn, $sqlinsert);

        header('Location: index.php');

    } elseif ($save === 'pilne') {

        require_once("dbconnect.php");
        
        $sql = "Select [Status] from [dbo].[Parts]
      WHERE Projekt='$projekt' and Pozycja='$detal'";
      $result = sqlsrv_query($conn, $sql);
      while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $status=$row['Status'];
      }
      echo $status;
        if($status==0){
        $sql = "UPDATE [dbo].[Parts]
        SET 
           [Status] = 1
      WHERE Projekt='$projekt' and Pozycja='$detal'";
      $result = sqlsrv_query($conn, $sql);
      }else{
        $sql = "UPDATE [dbo].[Parts]
        SET 
           [Status] = 0
      WHERE Projekt='$projekt' and Pozycja='$detal'";
      $result = sqlsrv_query($conn, $sql);
      }
      header('Location: index.php');
      
    } else {
        
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

        header('Location: index.php');

        }catch (Exception $e) {
    // Obsługa wyjątku
    echo "Wystąpił błąd: " . $e->getMessage();
}
        
    }
}
?>
