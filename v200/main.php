<?php require_once '../auth.php'; ?>
<?php
require_once("../dbconnect.php");
$projekt=isset($_GET['keywords']) ? $_GET['keywords'] : '';
if(empty($projekt)){

$projekt='Projekt...';
$sql="";
}else{
    $sql="SELECT distinct
    Count(b.[Name]) as otowry,
      a.[Diameter]
     ,b.[ProjectName]
     ,b.[Name]
     ,max(b.[AmountNeeded]) as need
     ,max(b.[AmountDone]) as done
    
    FROM [PartCheck].[dbo].[Hole_V200] a
    
    
    
    inner JOIN [PartCheck].[dbo].[Product_V200] b on (a.ProductId = b.Id)
    where b.[ProjectName]='$projekt'
    group by a.[Diameter]
     ,b.[ProjectName]
     ,b.[Name]";
}
 $datas = sqlsrv_query($conn, $sql);
 $datas1 = sqlsrv_query($conn, $sql);
 $sumNeedByDiameter = array();
 $sumDoneByDiameter = array();
 
 // Inicjalizacja zmiennych do przechowywania sumy otowry pomnożonej przez sumę need i done dla tych samych wartości Diameter
 $sumOtowryTimesNeedByDiameter = array();
 $sumOtowryTimesDoneByDiameter = array();
 
 // Sumowanie wartości dla tych samych wartości Diameter
 while ($row = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
  $diameter = $row['Diameter'];

  if (!isset($sumNeedByDiameter[$diameter])) {
      $sumNeedByDiameter[$diameter] = 0;
  }
  $sumNeedByDiameter[$diameter] += $row['need'];

  if (!isset($sumDoneByDiameter[$diameter])) {
      $sumDoneByDiameter[$diameter] = 0;
  }
  $sumDoneByDiameter[$diameter] += $row['done'];

  if (!isset($sumOtowryTimesNeedByDiameter[$diameter])) {
      $sumOtowryTimesNeedByDiameter[$diameter] = 0;
  }
  $sumOtowryTimesNeedByDiameter[$diameter] += $row['need'] * $row['otowry'];

  if (!isset($sumOtowryTimesDoneByDiameter[$diameter])) {
      $sumOtowryTimesDoneByDiameter[$diameter] = 0;
  }
  $sumOtowryTimesDoneByDiameter[$diameter] += $row['done'] * $row['otowry'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'globalhead.php'; ?>
</head>
<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php require_once("navbar.php"); ?>
<br /><br /><br /><br />
<!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<?php if(isSidebar()==0){ ?>
  <div class="container-fluid" style="width:80%;margin-left:16%;">
    <?php }else if(isSidebar()==1){ ?>
        <div class="container-fluid" style="width:90%; margin: 0 auto;">
        <?php } ?>
<form method="get" action="">
            <input type="text" class="form-control form-control" name="keywords" oninput="convertToUppercase(this)" placeholder="<?php echo $projekt; ?>" style="width:20%;float:left;"> 
            <button class="btn btn-primary" type="submit" style="float:left;">Szukaj</button>
            <div style="clear:both;"></div>
</form>
<br />
<div class="table-responsive">
            <table class="table table-xl table-hover table-striped" id="mytable">
  <thead>
    <tr>
      <th scope="col">Projekt</th>
      <th scope="col">Detal</th>
      <th scope="col">Ilość Zaplanowana / Zrobiona</th>
      <th scope="col">średnica</th>
      <th scope="col">liczba otworów</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {

 ?>
    <tr>
      <td><?php echo $data['ProjectName']; ?></td>
      <td><?php echo $data['Name']; ?></td>
      <td><?php echo $data['need'].' / '.$data['done']; ?></td>
      <td><?php echo $data['Diameter']; ?></td>
      <td><?php echo $data['otowry']; ?></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot class='table-success'>
  <?php
                foreach ($sumNeedByDiameter as $diameter => $sumNeed) {
               
                ?>
  <tr>
    <center><td colspan='2'>SUMA</td></center>
    <td><?php  echo $sumNeed . ' / ' . $sumDoneByDiameter[$diameter]; ?></td>
    <td><?php  echo $diameter; ?></td>
    <td><?php  echo $sumOtowryTimesNeedByDiameter[$diameter] . ' / ' . $sumOtowryTimesDoneByDiameter[$diameter]; ?></td>
        </tr>
  <?php } ?>

  </tfoot>
</table>


</div>
    </div>
    </div>
    </div>
    <?php include 'globalnav.php'; ?>
</body>
<script>
    function convertToUppercase(inputElement) {
      inputElement.value = inputElement.value.toUpperCase();
    }
</script>
</html>