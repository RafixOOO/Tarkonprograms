<?php

if (isset($_POST['selectedrow'])) {
  $selectedRows = json_decode($_POST['selectedrow'], true);
  require_once("dbconnect.php");
  $length = count($selectedRows);
    $ilosc;
    $dlugosc;
    $wykonawca;
  for($i=0;$i<$length;$i++){
    list($projekt, $detal, $osoba) = explode(',', $selectedRows[$i]);
    
    $sql = "SELECT imie_nazwisko FROM dbo.Persons WHERE identyfikator = $osoba";
        $result = sqlsrv_query($conn, $sql);
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
          $wykonawca=$row['imie_nazwisko'];
      }
        $sql = "Select distinct sum(Ilosc_zrealizowana) as ilosc, sum(Dlugosc_zrealizowana) as dlugosc from dbo.Product_Recznie where Projekt='$projekt' and Pozycja='$detal'";
        $result = sqlsrv_query($conn, $sql);
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $ilosc=$row['ilosc'];
            $dlugosc= $row['dlugosc'];
        }

        $sql = "Select distinct sum(Ilosc) as ilosc, sum(Dlugosc) as dlugosc from dbo.Parts where Projekt='$projekt' and Pozycja='$detal' ";
        $result = sqlsrv_query($conn, $sql);
        if ($result === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $ilosc = $row['ilosc']-$ilosc;
            $dlugosc= $row['dlugosc']-$dlugosc;
            $sqlinsert = "INSERT INTO dbo.Product_Recznie (Projekt, Pozycja, Ilosc_zrealizowana, Dlugosc_zrealizowana, Maszyna, Osoba) VALUES ('{$projekt}', '{$detal}', '{$ilosc}', '{$dlugosc}', 'Kooperacyjnie' , '{$wykonawca}')";

        sqlsrv_query($conn, $sqlinsert);
        }
  }
}
?>
