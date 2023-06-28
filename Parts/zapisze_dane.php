<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projekt = $_POST["project"];
    $detal = $_POST["detal"];
    $ilosc = $_POST["ilosc"];
    $dlugosc = $_POST["dlugosc"];
    $wykonawca = $_POST["osoba"];
    $maszyna = $_POST["maszyna"];
    $status;
    
    if ($_POST['save'] === 'piece') {

        require_once("dbconnect.php");
        $sqlinsert = "INSERT INTO dbo.Product_Recznie (Projekt, Pozycja, Ilosc_zrealizowana, Dlugosc_zrealizowana, Osoba, Maszyna) VALUES ('{$projekt}', '{$detal}', '{$ilosc}', '{$dlugosc}', '{$wykonawca}', '{$maszyna}')";

        sqlsrv_query($conn, $sqlinsert);

        header('Location: index.php');

    } elseif ($_POST['save'] === 'all') {
        require_once("dbconnect.php");
        $sql = "Select distinct sum(Ilosc_zrealizowana) as ilosc, sum(Dlugosc_zrealizowana) as dlugosc from dbo.Product_Recznie where Projekt='$projekt' and Pozycja='$detal'";
        $result = sqlsrv_query($conn, $sql);
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $ilosc=$row['ilosc'];
            $dlugosc= $row['dlugosc'];
        }

        $sql = "Select Ilosc as ilosc, Dlugosc as dlugosc from dbo.Parts where Projekt='$projekt' and Pozycja='$detal' ";
        $result = sqlsrv_query($conn, $sql);
        if ($result === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $ilosc = $row['ilosc']-$ilosc;
            $dlugosc= $row['dlugosc']-$dlugosc;
            $sqlinsert = "INSERT INTO dbo.Product_Recznie (Projekt, Pozycja, Ilosc_zrealizowana, Dlugosc_zrealizowana, Osoba, Maszyna) VALUES ('{$projekt}', '{$detal}', '{$ilosc}', '{$dlugosc}', '{$wykonawca}', '{$maszyna}')";

        sqlsrv_query($conn, $sqlinsert);
        }
        
        header('Location: index.php');

    } elseif ($_POST['save'] === 'pilne') {

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
      
    }
}
?>
