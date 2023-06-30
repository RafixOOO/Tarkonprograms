<?php

if (isset($_POST['selectedrow'])) {
  $selectedRows = json_decode($_POST['selectedrow'], true);
  require_once("dbconnect.php");
  $length = count($selectedRows);
    $status;
  for($i=0;$i<$length;$i++){
    list($projekt, $detal) = explode(',', $selectedRows[$i]);

        
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
        sqlsrv_query($conn, $sql);
      }else{
        $sql = "UPDATE [dbo].[Parts]
        SET 
           [Status] = 0
      WHERE Projekt='$projekt' and Pozycja='$detal'";
      sqlsrv_query($conn, $sql);
      }
    
   
  }
}
?>