<?php

require_once("dbconnect.php");


$projekt=isset($_GET['keywords']) ? $_GET['keywords'] : '';


$sqlmesser = "SELECT Distinct
p.[Status] as status,
p.[Projekt] as ProjectName
,p.[Zespol] AS zespol
,p.[Pozycja] as Detal
,p.[Ilosc] as ilosc,
(select sum(p1.[Ilosc])
from [PartCheck].[dbo].[Parts] p1
where p1.[Pozycja]=b.[PartName] COLLATE Latin1_General_CS_AS) as ilosc_full
,sum(b.[QtyProgram]) AS ilosc_zrealizowana
from [PartCheck].[dbo].[PartArchive_Messer] as b INNER JOIN [PartCheck].[dbo].[Parts] as p ON b.[PartName] = p.[Pozycja] COLLATE Latin1_General_CS_AS
where p.[Pozycja] !='' and p.[Projekt]='$projekt'
group by p.[Pozycja],p.[Projekt], p.[Status], b.[PartName],p.Zespol,p.[Ilosc]";
$datasmesser = sqlsrv_query($conn, $sqlmesser);


$sqlv630 = "SELECT Distinct
p.[Status] as status,
p.[Projekt] as ProjectName
,p.[Zespol] AS zespol
,p.[Pozycja] as Detal
,p.[Ilosc] as ilosc,
(select sum(p1.[Ilosc])
from [PartCheck].[dbo].[Parts] p1
where p1.[Pozycja]=b.[Name]) as ilosc_full
,sum(b.[AmountDone]) AS ilosc_zrealizowana
from [PartCheck].[dbo].[Product_V630] as b INNER JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja]
where p.[Pozycja] !='' and p.[Projekt]='$projekt'
group by p.[Pozycja],p.[Projekt], p.[Status], b.[Name],p.Zespol,p.[Ilosc]
";
$datasv630 = sqlsrv_query($conn, $sqlv630);

$sqlrecznie = "SELECT Distinct
p.[Status] as status,
p.[Projekt] as ProjectName
,p.[Zespol] AS zespol
,p.[Pozycja] as Detal
,p.[Ilosc] as ilosc,
(select sum(p1.[Ilosc])
from [PartCheck].[dbo].[Parts] p1
where p1.[Pozycja]=b.[Pozycja] COLLATE Latin1_General_CS_AS) as ilosc_full
,sum(b.[Ilosc_zrealizowana]) AS ilosc_zrealizowana
from [PartCheck].[dbo].[Product_Recznie] as b right JOIN [PartCheck].[dbo].[Parts] as p ON b.[Pozycja] = p.[Pozycja] COLLATE Latin1_General_CS_AS
where NOT EXISTS (
    SELECT 1
    FROM dbo.PartArchive_Messer m
    WHERE p.Pozycja = m.PartName COLLATE Latin1_General_CS_AS
)
AND NOT EXISTS (
    SELECT 1
    FROM dbo.Product_V630 v
    WHERE p.Pozycja = v.Name
) and p.[Pozycja] !='' and p.[Projekt]='$projekt'
group by p.[Pozycja],p.[Projekt], p.[Status], b.[Pozycja],p.Zespol,p.[Ilosc]";
$datasrecznie = sqlsrv_query($conn, $sqlrecznie);



$sqlproject = "SELECT Distinct
max(p.[Id]) as id,
p.[Projekt] as ProjectName,
p.[Zespol] AS zespol
,(select p1.[Ilosc]
from [PartCheck].[dbo].[Parts] p1
where p.[Zespol]=p1.Zespol and p1.[Pozycja] ='') as ilosc
from [PartCheck].[dbo].[Parts] as p
where p.[Pozycja] !='' and p.[Projekt]='$projekt'
group by p.[Projekt], p.[Zespol],p.[Ilosc]";
$datasproject = sqlsrv_query($conn, $sqlproject);

$dataresult1 = array();

while ($dataot = sqlsrv_fetch_array($datasrecznie, SQLSRV_FETCH_ASSOC)) {
  $dataresult1[] = $dataot;
}

while ($datamesser = sqlsrv_fetch_array($datasv630, SQLSRV_FETCH_ASSOC)) {
  $dataresult1[] = $datamesser;
}

while ($data = sqlsrv_fetch_array($datasmesser, SQLSRV_FETCH_ASSOC)) {
  $dataresult1[] = $data;
}

?>
<!DOCTYPE html>

<html>

<head>

  <?php 
  require_once('globalhead.php') ;
  require_once('../auth.php');
  ?>

<style>
    tr.hide-table-padding td {
  padding: 0;
  }

  .expand-button {
    position: relative;
  }

  .accordion-toggle .expand-button:after
  {
    position: absolute;
    left:.75rem;
    top: 50%;
    transform: translate(0, -50%);
    content: '-';
  }
  .accordion-toggle.collapsed .expand-button:after
  {
    content: '+';
  }

    .btn-group {
      float: right;
    }
    </style>
</head> 

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
<?php require_once('globalnav.php'); ?>
  <div class="container-xl">
  <form method="get" action="">
          <div class="input-group">
            <input type="text" class="form-control" name="keywords" value="<?php echo $projekt; ?>" oninput="convertToUppercase(this)" placeholder="Nazwa projektu..."> <button class="btn btn-primary" type="submit">Szukaj</button>
          </div>
          </form>
            <br /><br />
            <div class="btn-group">
            <button id="toggleButton" class="btn btn-primary" onclick="toggleAll()">Rozwiń</button>
</div>
<div style="clear:both;"></div>
            <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Zespół</th>
            <th scope="col">Ilość</th>
          </tr>
        </thead>
        <tbody>
  <?php while ($data = sqlsrv_fetch_array($datasproject, SQLSRV_FETCH_ASSOC)): 
    $orange= 0;
    $green = 0;
    $dark=0;
    ?>
    <tr class="accordion-toggle collapsed "
      data-bs-toggle="collapse"
      data-bs-target="<?php echo '#collapse'.$data['id']; ?>"
      aria-controls="<?php echo 'collapse'.$data['id']; ?>"
    >
      <td class="expand-button"></td>
      <td id="<?php echo 'collapse1'.$data['id']; ?>"><?php echo $data['zespol']; ?></td>
      <td><?php echo $data['ilosc']; ?></td>
    </tr>
    </div>
    <tr class="hide-table-padding">
      <td></td>
      <td colspan="2">
        <div id="<?php echo 'collapse'.$data['id']; ?>" class="collapse p-3">
          <?php foreach ($dataresult1 as $data1): ?>
            <?php if($data['zespol']==$data1['zespol']): 
              if($data1['ilosc_full']<=$data1['ilosc_zrealizowana'] and $data1['ilosc_zrealizowana']!=''){
                $green=$green+1;
              ?>
              <div class="row text-success">
              <div class="col-2"><?php echo $data1['Detal']; ?></div>
                <div class="col-6"><?php echo $data1['ilosc']; ?></div>
              </div>
            <?php 
              } else if($data1['ilosc']<=$data1['ilosc_zrealizowana']and $data1['ilosc_zrealizowana']!=''){
                $orange=$orange+1;
              ?>
              <div class="row text-warning">
                <div class="col-2"><a class='text-warning' href="main.php?keywords=<?php echo $data['zespol']; ?>+<?php echo $data1['Detal']; ?>&dataFrom=&dataTo=&page_size=25"><?php echo $data1['Detal']; ?></a></div>
                <div class="col-6"><?php echo $data1['ilosc']; ?></div>
              </div>
            <?php } else if($data1['ilosc']>$data1['ilosc_zrealizowana']){ 
              $dark=$dark+1;
              ?>
              <div class="row">
              <div class="col-2"><a class='text-dark' href="main.php?keywords=<?php echo $data['zespol']; ?>+<?php echo $data1['Detal']; ?>&dataFrom=&dataTo=&page_size=25"><?php echo $data1['Detal']; ?></a></div>
              <div class="col-6"><?php echo $data1['ilosc']; ?></div>
            </div>
            <?php 
            }
            endif;
            ?>
          <?php endforeach; ?>
        </div>
      </td>
    </tr>
  <?php 
        if($dark==0 and $orange==0){
          $id = 'collapse1' . $data['id'];
          echo "<script>";
          echo "var row6 = document.getElementById('" . $id . "');";
          echo "if (row6) {";
          echo "  row6.classList.add('text-success');";
          echo "}";
          echo "</script>";
        } else if($orange>1 and $dark==0){
          $id = 'collapse1' . $data['id'];
          echo "<script>";
          echo "var row6 = document.getElementById('" . $id . "');";
          echo "if (row6) {";
          echo "  row6.classList.add('text-warning');";
          echo "}";
          echo "</script>";
        } 
        else if($dark>1){
          continue;
        }
  endwhile; ?>
</tbody>
      </table>
</div>
</div>
</body>
<script>
  function convertToUppercase(inputElement) {
      inputElement.value = inputElement.value.toUpperCase();
    }

    function toggleAll() {
      var collapseElements = document.querySelectorAll('.collapse');
      var expanded = false;

      // Sprawdź, czy którykolwiek z elementów jest rozwinięty
      collapseElements.forEach(function(element) {
        if (element.classList.contains('show')) {
          expanded = true;
        }
      });

      // Zmiana stanu wszystkich elementów w zależności od wartości zmiennej "expanded"
      if (expanded) {
        collapseElements.forEach(function(element) {
          element.classList.remove('show');
        });
        document.getElementById('toggleButton').innerText = 'Rozwiń';
      } else {
        collapseElements.forEach(function(element) {
          element.classList.add('show');
        });
        document.getElementById('toggleButton').innerText = 'Zwiń';
      }
    }
</script>
</html>