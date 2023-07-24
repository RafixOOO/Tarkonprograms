<?php
require_once("dbconnect.php");
$projekt=isset($_GET['keywords']) ? $_GET['keywords'] : '';
if(empty($projekt)){


$sql="SELECT
Count(b.[Name]) as otowry,
  a.[Diameter]
 ,b.[ProjectName]
 ,b.[Name]
 ,b.[AmountNeeded] as need
 ,b.[AmountDone] as done

FROM [PartCheck].[dbo].[Hole_V200] a



inner JOIN [PartCheck].[dbo].[Product_V200] b on (a.ProductId = b.Id)

group by a.[Diameter]
 ,b.[ProjectName]
 ,b.[Name],b.[AmountNeeded],b.[AmountDone]";
}else{
    $sql="SELECT
Count(b.[Name]) as otowry,
  a.[Diameter]
 ,b.[ProjectName]
 ,b.[Name]
 ,b.[AmountNeeded] as need
 ,b.[AmountDone] as done

FROM [PartCheck].[dbo].[Hole_V200] a



inner JOIN [PartCheck].[dbo].[Product_V200] b on (a.ProductId = b.Id)
where b.[ProjectName]='$projekt'
group by a.[Diameter]
 ,b.[ProjectName]
 ,b.[Name],b.[AmountNeeded],b.[AmountDone]";
}
 $datas = sqlsrv_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'globalhead.php'; ?>
</head>
<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">

<?php include 'globalnav.php'; ?>
<div class="container">
<form method="get" action="">
          <div class="input-group">
            <input type="text" class="form-control" name="keywords" value="<?php echo $projekt; ?>" oninput="convertToUppercase(this)" placeholder="Projekt..."> <button class="btn btn-primary" type="submit">Szukaj</button>
          </div>
</form>
<br />
<table class="table table-sm">
  <thead>
    <tr>
      <th scope="col">Projekt</th>
      <th scope="col">Detal</th>
      <th scope="col">Detale need / done</th>
      <th scope="col">Średnica</th>
      <th scope="col">Liczba Otworów</th>
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
</table>
</div>
    </div>
    </div>
    </div>
</body>
<script>
    function convertToUppercase(inputElement) {
      inputElement.value = inputElement.value.toUpperCase();
    }
</script>
</html>