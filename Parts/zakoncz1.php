<?php
require_once('../auth.php');
if (isset($_POST['selectedrow'])) {
  $selectedRows = json_decode($_POST['selectedrow'], true);
  require_once("../dbconnect.php");

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

        $sql = "Select distinct COALESCE(SUM(p.Ilosc_zrealizowana), c.CNT) AS ilosc, 
        COALESCE(SUM(p.Dlugosc_zrealizowana), (c.CNT * c.CZDLU)) AS dlugosc from PartCheck.dbo.cutlogic c full join PartCheck.dbo.Product_Recznie p on p.Pozycja=c.CZESC where (c.PROJEKT='$projekt' and c.CZESC='$detal') or (p.Projekt='$projekt' and p.Pozycja='$detal') group by c.CNT, c.CZDLU";
            $result = sqlsrv_query($conn, $sql);
            if ($result === false) {
                die(print_r(sqlsrv_errors(), true));
            }
              
          while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
              $ilosc = $row['ilosc']-$ilosc;
             $dlugosc = $row['dlugosc']-$dlugosc;
            
            $sqlinsert = "INSERT INTO dbo.Product_Recznie (Projekt, Pozycja, Ilosc_zrealizowana, Dlugosc_zrealizowana, Maszyna, Osoba) VALUES ('{$projekt}', '{$detal}', '{$ilosc}', '{$dlugosc}', 'Pila', '{$wykonawca}')";
    
        sqlsrv_query($conn, $sqlinsert);
        }
        if($_SESSION['imie_nazwisko']==""){
          logUserActivity($wykonawca,'Zaktualizował aplikację parts: '.$detal);
        }else{
          logUserActivity($_SESSION['imie_nazwisko'],'Zaktualizował aplikację parts: '.$detal);
        }
       
}
}
?>
