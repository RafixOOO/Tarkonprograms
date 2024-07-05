<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'globalhead.php'; 
    require_once('../auth.php');?>
    <style>
      tr.selected {
    background-color: #cce5ff; /* Kolor tła dla zaznaczonych wierszy */
}
      </style>
</head>
<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
  <!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<?php 
require_once 'cutlogic.php';
?>

<table id="myTable" class="table table-sm table-hover table-striped table-bordered" style="font-size: calc(9px + 0.390625vw); width:80%; margin-left: auto; margin-right:auto;">


<thead>
  <tr>
    <th scope="col">ItemName</th>
    <th scope="col">Batch Quantity</th>
    <th scope="col">InvntryUom</th>
    <th scope="col">DistNumber</th>
    <th scope="col">Projekt</th>
    <th scope="col">Rezerwacja</th>
    <th scope="col">U_Nrwytopu</th>
    <th scope="col">LastPunPrc</th>
  </tr>
</thead>
<tbody>
<?php $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) { 
?>  
<tr value="<?php echo $row['ItemCode']; ?>">
<td><?php echo $row['ItemName']; ?></td>
<td><?php echo $row['Batch Quantity']; ?></td>
<td><?php echo $row['InvntryUom']; ?></td>
<td><?php echo $row['DistNumber']; ?></td>
<td><?php echo $row['Projekt']; ?></td>
<td><?php echo $row['Rezerwacja']; ?></td>
<td><?php echo $row['U_NRWYTOPU']; ?></td>
<td><?php echo $row['LastPurPrc']; ?></td>
</tr>
  <?php } ?>
</tbody>
</table>
<?php include 'globalnav.php'; ?>
</body>
</html>