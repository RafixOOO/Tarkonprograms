<?php 
require_once 'vendor/autoload.php';

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;

// Now you can use the Utils class

require_once 'othersql.php';

$dataresult = array();

while ($dataot = sqlsrv_fetch_array($dataother, SQLSRV_FETCH_ASSOC)) {
  $dataresult[] = $dataot;
}

require_once 'messer.php';

while ($datamesser = sqlsrv_fetch_array($datasmesser, SQLSRV_FETCH_ASSOC)) {
  $dataresult[] = $datamesser;
}

require_once 'v630.php';

while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
  $dataresult[] = $data;
}

$myVariable = isset($_GET['myCheckbox']) ? 1 : 0;
$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
$keywordArray = explode(' ', $keywords);
$dataFrom = isset($_GET['dataFrom']) ? $_GET['dataFrom'] : '';
$dataTo = isset($_GET['dataTo']) ? $_GET['dataTo'] : '';
$filteredData = array_filter($dataresult, function ($item) use ($keywordArray, $dataFrom, $dataTo, $myVariable) {
  if ($keywordArray !== '') {
  foreach ($keywordArray as $keyword) {
    $keyword = trim($keyword);
    
      $columnsToSearch = ['ProjectName', 'zespol', 'Detal', 'maszyna', 'wykonal' ]; // Dodaj więcej kolumn, jeśli jest potrzebne
      $matchesKeyword = false;
      foreach ($columnsToSearch as $column) {
        if (($item['ilosc_zrealizowana'] >= $item['ilosc'] or $item['lok'] == 1) and $myVariable == 0) {
          continue;
        }
        $columnValue = $item[$column] instanceof DateTime ? $item[$column]->format('Y-m-d H:i:s') : $item[$column];
        if (stripos($columnValue, $keyword) !== false) {
          $matchesKeyword = true;
          break;
        }
      }
      if (!$matchesKeyword) {
        return false;
      }
  }
}

if($myVariable == 0 and $keywordArray == ''){
  if (($item['ilosc_zrealizowana'] >= $item['ilosc'] or $item['lok'] == 1)) {
    return false;
  }
}

  if ($dataFrom !== '') {
    $dataFrom = new DateTime($dataFrom);
    $itemData = $item['data'] instanceof DateTime ? $item['data'] : new DateTime($item['data']);
    if ($itemData < $dataFrom) {
      return false;
    }
  }

  if ($dataTo !== '') {
    $dataTo = new DateTime($dataTo);
    $itemData = $item['data'] instanceof DateTime ? $item['data'] : new DateTime($item['data']);
    if ($itemData > $dataTo) {
      return false;
    }
  }

  return true;
});
$pageSizeOptions = [25, 100, 500, 1000];
$pageSize = isset($_GET['page_size']) ? $_GET['page_size'] : 25;
$pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
$showAll = $pageSize == count($filteredData); // Sprawdzamy, czy wartość jest równa -1, aby określić, czy "ALL" jest wybrane

if ($showAll) {
  $pageSize = count($filteredData);
} else {
  $pageSize = (int)$pageSize;
  $pageSize = max(1, $pageSize); // Upewniamy się, że $pageSize jest większe lub równe 1
}
$adapter = new ArrayAdapter($filteredData);
$pagerfanta = new Pagerfanta($adapter);
$pagerfanta->setMaxPerPage($pageSize);
$pagerfanta->setCurrentPage($pageNumber);

$currentPageResults = $pagerfanta->getCurrentPageResults();

$sumaIlosc = array_sum(array_column($filteredData, 'ilosc'));
$sumaIloscZrealizowana = array_sum(array_column($filteredData, 'ilosc_zrealizowana'));
try{
$sumaKolumnyJeden = 0;
foreach ($filteredData as $row) {
  if ($row['lok'] == 1) {
    // Sumowanie kolumny gdy kolumna 'lok' jest równa 1
    $sumaKolumnyJeden=$sumaKolumnyJeden + $row['ilosc_zrealizowana'];
}
}
}catch(error){
  $sumaKolumnyJeden=0;
}

$jsonData = json_encode($filteredData);


$sumaZrealizowanaMiesiace = array();

// Iteruj po danych i sumuj wartości 'ilość_zrealizowana' dla poszczególnych miesięcy
$sumaZrealizowanaTygodnie = [];
$sumaZrealizowanaMiesiace = [];
$sumaZrealizowanaDni = [];

foreach ($filteredData as $row) {
    $dateTime = $row['data'];
    if ($dateTime instanceof DateTime) {
        $weekNumber = $dateTime->format('W');
        $year = $dateTime->format('Y');
        $weekYear = $year . ' W' . $weekNumber;

        if (!isset($sumaZrealizowanaTygodnie[$weekYear])) {
            $sumaZrealizowanaTygodnie[$weekYear] = 0;
        }

        $sumaZrealizowanaTygodnie[$weekYear] += $row['ilosc_zrealizowana'];

        uksort($sumaZrealizowanaTygodnie, function($a, $b) {
          $weekA = substr($a, strpos($a, 'W') + 1);
          $weekB = substr($b, strpos($b, 'W') + 1);
          $yearA = substr($a, 0, strpos($a, ' W'));
          $yearB = substr($b, 0, strpos($b, ' W'));
      
          return ($yearA <=> $yearB) ?: ($weekA <=> $weekB);
      });

        $monthYear = $dateTime->format('M Y');

        if (!isset($sumaZrealizowanaMiesiace[$monthYear])) {
            $sumaZrealizowanaMiesiace[$monthYear] = 0;
        }

        $sumaZrealizowanaMiesiace[$monthYear] += $row['ilosc_zrealizowana'];

        uksort($sumaZrealizowanaMiesiace, function($a, $b) {
          $dateTimeA = DateTime::createFromFormat('M Y', $a);
          $dateTimeB = DateTime::createFromFormat('M Y', $b);
      
          if ($dateTimeA instanceof DateTime && $dateTimeB instanceof DateTime) {
              return $dateTimeA <=> $dateTimeB; // Porównanie dat
          }
      
          return 0; // W przypadku błędnych danych, zachowaj oryginalną kolejność
      });

        $date = $dateTime->format('Y-m-d');

        if (!isset($sumaZrealizowanaDni[$date])) {
            $sumaZrealizowanaDni[$date] = 0;
        }

        $sumaZrealizowanaDni[$date] += $row['ilosc_zrealizowana'];

        uksort($sumaZrealizowanaDni, function($a, $b) {
          $dateA = new DateTime($a);
          $dateB = new DateTime($b);
          
          if ($dateA < $dateB) {
              return -1;
          } elseif ($dateA > $dateB) {
              return 1;
          } else {
              return 0;
          }
      });
    }
}


if (count($sumaZrealizowanaMiesiace) > 1) {
    // Wykres miesięcy
    $data = array(
        'labels' => array_keys($sumaZrealizowanaMiesiace),
        'datasets' => array(
            array(
                'label' => 'Suma ilość na miesiąc',
                'data' => array_values($sumaZrealizowanaMiesiace),
                'borderColor' => 'red', // Dostarcz ręcznie odpowiedni kolor
                'backgroundColor' => 'rgba(255, 0, 0, 0.5)', // Dostarcz ręcznie odpowiedni kolor z przezroczystością
                'pointStyle' => 'circle',
                'pointRadius' => 10,
                'pointHoverRadius' => 15
            )
        )
    );
  }else if (count($sumaZrealizowanaTygodnie) > 1) {
      // Wykres tygodni
      $data = array(
          'labels' => array_keys($sumaZrealizowanaTygodnie),
          'datasets' => array(
              array(
                  'label' => 'Suma ilość na tydzień',
                  'data' => array_values($sumaZrealizowanaTygodnie),
                  'borderColor' => 'red', // Dostarcz ręcznie odpowiedni kolor
                  'backgroundColor' => 'rgba(255, 0, 0, 0.5)', // Dostarcz ręcznie odpowiedni kolor z przezroczystością
                  'pointStyle' => 'circle',
                  'pointRadius' => 10,
                  'pointHoverRadius' => 15
              )
          )
      );
} else {
    // Wykres dni
    $data = array(
        'labels' => array_keys($sumaZrealizowanaDni),
        'datasets' => array(
            array(
                'label' => 'Suma ilość na dzień',
                'data' => array_values($sumaZrealizowanaDni),
                'borderColor' => 'red', // Dostarcz ręcznie odpowiedni kolor
                'backgroundColor' => 'rgba(255, 0, 0, 0.5)', // Dostarcz ręcznie odpowiedni kolor z przezroczystością
                'pointStyle' => 'circle',
                'pointRadius' => 10,
                'pointHoverRadius' => 15
            )
        )
    );
}

$jsonData1 = json_encode($data);


?>
<!DOCTYPE html>

<html>

<head>

  <?php require_once('globalhead.php') ;
  require_once('../auth.php');
  ?>
  <style>

    .bottom-banner1 {
  background-color: orange;
  position: fixed;
  bottom: 8px;
  right: 40%;
  font-size: 18px;
  width:15%;
  text-align: center;
border-radius: 10px;
}

.verticalrotate{
  position:fixed;
  bottom:50%;
  left:84.5%;
  width: 30%;
  transform: rotate(-90deg);
}

#loader-wrapper{
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: #ECF0F1;
   display: none; 
}

.js .load, .js #loader-wrapper {
  display: block;
}

#myChart, #myChart1 {
   width: 50%;
   height: 350px;
}
.label {/*from  w  ww. ja  v  a 2  s  .  co  m*/
   text-align: center;
   width: 600px;
   font-size: 20px;
   font-weight: bold;
   margin: 20px;
}
  </style>

</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php require_once('globalnav.php') ?>
  <div class="container-fluid">
    <?php if(!isLoggedIn()){ ?>
  <div class="progress verticalrotate">
  <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
</div>
    <?php } ?>

    
    
<div class="mb-3" style="float:right;">

        <form id="myForm1" method="get" action="">
          <div class="input-group">
            <input type="text" class="form-control" name="keywords" value="<?php echo $keywords; ?>" placeholder="Nazwa..." autofocus> <button class="btn btn-primary" type="submit">Szukaj</button>
          </div>
          od: <input type="date" value="<?php echo $dataFrom; ?>" name="dataFrom"> do: <input type="date" value="<?php echo $dataTo; ?>" name="dataTo">
          </div>
          <div class="form-group" style="float:left;">
          <label for="pageSizeSelect">Liczba wyników na stronie:</label>
          <select class="form-control" id="pageSizeSelect" name="page_size">
          <?php foreach ($pageSizeOptions as $option): ?>
            <option value="<?php echo $option; ?>" <?php echo $pageSize === $option ? 'selected' : ''; ?>>
              <?php echo $option; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label for="checkbox">Pokaż zakończone: </label>
          <input type="checkbox" name="myCheckbox" id="checkbox" <?php if ($myVariable == 1) echo 'checked'; ?>>
        </div>
        </form>
      
      <div style="clear:both;"></div>
      <div class="table-responsive">
    <table id="myTable" class="table table-sm table-hover table-striped table-bordered" style="font-size: calc(9px + 0.390625vw)">


      <thead>
        <tr>
          <th scope="col">Project</th>
          <th scope="col" style="width:10em;">Assembly</th>
          <th scope="col">Part</th>
          <th scope="col">Amount Need / Done</th>
          <th scope="col">V200</th>
          <th scope="col">Machine</th>
          <th scope="col">Dimension</th>
          <th scope="col">Material</th>
          <th scope="col">Length</th>
          <th scope="col">Length Done</th>
          <th scope="col">Weight</th>
          <th scope="col">Weight Done</th>
          <th scope="col">Description</th>
          <th scope="col">Date</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($currentPageResults as $data): 
        if ($data['ilosc'] == 0 or $data['ilosc'] == '') {
            $szer = 0;
          } else {
            $szer = $data['ilosc_zrealizowana'] / $data['ilosc'] * 100;
          }

          if ($data['lok'] == 1 or $szer >= 100) {
            if($data['lok'] == 1){
              echo "<tr class='table-danger'>";
            }else{
              echo "<tr>";
            }
            
             } else if(($data['maszyna']=="" or $data['maszyna']=="Recznie" or $data['maszyna']=="Kooperacyjnie" or $data['maszyna']=="Pila") and $szer < 100) {
           echo '<tr id="myRow" onclick="handleClick(this);">';
             }
          ?>
        <td id="project"><?php echo $data['ProjectName']; ?></td>
            <td id="zespol"><?php if ($data['status'] == 1) {
                              echo $data['zespol'] . " <img src='../static/triangle.svg' /></img>";
                            } else {
                              echo $data['zespol'];
                            } ?></td>
            <td id="detal"><?php echo $data['Detal']; ?></td>
            <td >
              <div class="progress" style="height:25px;font-size: 16px;">
                <?php if ($szer <= 100) { ?>
                  <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo  $data['ilosc_zrealizowana']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $data['ilosc']; ?>'><?php echo $data['ilosc_zrealizowana']; ?></div>
                  <span class='progress-bar bg-white text-dark' style='width:
                  <?php if (100 - $szer < 0) {
                    echo 0;
                  } else {
                    echo 100 - $szer;
                  } ?>%;'><?php echo $data['ilosc']; ?> </span>
                <?php } else { ?>
                  <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo  $data['ilosc_zrealizowana']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $data['ilosc']; ?>'><?php echo $data['ilosc'] . "/" . $data['ilosc_zrealizowana']; ?></div>
                <?php }
                ?>
            </td>
            <td><?php echo $data['ilosc_v200']."/".$data['ilosc_v200_zre']; ?></td>
            <td><?php echo $data['maszyna']; ?></td>
            <td><?php echo $data['profil']; ?></td>
            <td><?php echo $data['material']; ?></td>
            <td><?php echo $data['dlugosc']; ?></td>
            <td><?php echo $data['dlugosc_zre']; ?></td>
            <td><?php echo $data['Ciezar']; ?></td>
            <td><?php echo $data['Calk_ciez']; ?></td>
            <td><?php echo $data['import'].$data['uwaga'] . "," . $data['wykonal']; ?></td>
            <td><?php if ($data['data'] != "") {
                  echo $data['data']->format('Y-m-d H:i:s');
                } ?>
            </td>
          </tr>
        <?php endforeach; ?>
</tbody>
</table>




<table id="myTablereport" hidden>
<caption id="tableTitle"><?php echo $_GET['keywords']." (od: ".$_GET['dataFrom']." do: ".$_GET['dataTo'].") Ilość: ".$_GET['page_size']; ?></caption>
<thead>
  <tr>
  <th scope="col">Project</th>
          <th scope="col" style="width:10em;">Assembly</th>
          <th scope="col">Part</th>
          <th scope="col">Amount Need / Done</th>
          <th scope="col">V200</th>
          <th scope="col">Machine</th>
          <th scope="col">Dimension</th>
          <th scope="col">Material</th>
          <th scope="col">Length</th>
          <th scope="col">Length Done</th>
          <th scope="col">Weight</th>
          <th scope="col">Weight Done</th>
          <th scope="col">Description</th>
          <th scope="col">Date</th>
  </tr>
</thead>
<tbody>
<?php foreach ($currentPageResults as $data): 
  if ($data['ilosc'] == 0 or $data['ilosc'] == '') {
      $szer = 0;
    } else {
      $szer = $data['ilosc_zrealizowana'] / $data['ilosc'] * 100;
    }

    if ($szer >= 100  ) {
      echo "<tr>";
       } else if(($data['maszyna']=="" or $data['maszyna']=="Recznie" or $data['maszyna']=="Kooperacyjnie" or $data['maszyna']=="Pila") and $szer < 100) {
     echo '<tr id="myRow" onclick="handleClick(this);">';
       }
    ?>
  <td id="project"><?php echo $data['ProjectName']; ?></td>
      <td id="zespol"><?php if ($data['status'] == 1) {
                        echo $data['zespol'] . " <img src='../static/triangle.svg' /></img>";
                      } else {
                        echo $data['zespol'];
                      } ?></td>
      <td id="detal"><?php echo $data['Detal']; ?></td>
      <td >
        
      <?php if($data['ilosc_zrealizowana']==""){ echo $data['ilosc']."/0"; } else{ echo $data['ilosc']."/".$data['ilosc_zrealizowana']; } ?>
      </td>
      <td><?php echo $data['ilosc_v200']."/".$data['ilosc_v200_zre']; ?></td>
      <td><?php echo $data['maszyna']; ?></td>
      <td><?php echo $data['profil']; ?></td>
      <td><?php echo $data['material']; ?></td>
      <td><?php echo $data['dlugosc']; ?></td>
      <td><?php echo $data['dlugosc_zre']; ?></td>
      <td><?php echo $data['Ciezar']; ?></td>
      <td><?php echo $data['Calk_ciez']; ?></td>
      <td><?php echo $data['import'].$data['uwaga'] . "," . $data['wykonal']; ?></td>
      <td><?php if ($data['data'] != "") {
            echo $data['data']->format('Y-m-d H:i:s');
          } ?>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
</table>




      <div style="float:right">
<?php 

$view = new TwitterBootstrap4View();
$options = array(
    'prev_message' => '<',
    'next_message' => '>',
    'routeGenerator' => function ($page) {
        $queryString = $_SERVER['QUERY_STRING'];
        parse_str($queryString, $queryParams);
        $queryParams['page'] = $page;
        $newQueryString = http_build_query($queryParams);
        $url = $_SERVER['PHP_SELF'] . '?' . $newQueryString;
        return $url;
    },
);

echo $view->render($pagerfanta, $options['routeGenerator'], $options);
 ?>

</div>
<div class="btn-toolbar position-fixed" role="toolbar" aria-label="Toolbar with button groups" style="bottom:4%;">
  <div class="btn-group me-2 " role="group" aria-label="First group">

    <?php if (!isUserParts()) { ?>
  <?php if (!isUserPartsKier()) { ?>
  <button type="button"  onclick="localStorage.removeItem('number1'); location.reload();" class="btn btn-warning btn-lg">Wyjdź</button>
  <?php } ?>
  <?php if (isUserPartsKier()) { ?>
    <form method="POST" action="statuschange.php">
  <button type="Submit" onclick="localStorage.removeItem('number1')" class="btn btn-warning btn-lg" name="role" value="role_parts">Wyjdź</button>
        </form>
        <button type="button"  onclick="localStorage.removeItem('number1'); location.reload();" class="btn btn-warning btn-lg">Przełącz</button>
<?php }} ?>
<?php if (isUserPartsKier() && isUserParts()) { ?>
  <form method="POST" action="statuschange.php">
  <button type="Submit" onclick="localStorage.removeItem('number1')" class="btn btn-warning btn-lg" name="role" value="role_parts">Przełącz</button>
  <button type="button" onclick="sendSelectedRowsToPHP2()" class="btn btn-warning btn-lg">Kooperacyjnie</button>
        </form>
        <?php } ?>


  </div>
  <div class="btn-group me-2" role="group" aria-label="Second group">
    <?php if (!isUserParts()) { ?>
      <button type="button" onclick="sendSelectedRowsToPHP()" class="btn btn-warning btn-lg">Recznie</button>
      <button type="button" onclick="sendSelectedRowsToPHP1()" class="btn btn-warning btn-lg">Pila</button>
      <?php } ?>
      <?php if (isUserParts()) { ?>
      <button type="button" onclick="status()" class="btn btn-warning btn-lg">Status</button>
      <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#chartmodal">Raport</button>
      <?php } ?>
  </div>
</div>
<br /><br />
<?php if(isUserParts()){ ?>
  <div class="modal" id="chartmodal">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 60%; height: 90%;">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title">Raport</h4>
          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

<div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
  <div class="carousel-inner">
<center>
    <div class="carousel-item active">
    <div class="chart_containers d-block w-100" style="position: relative; height:40vh; width:80vw;">
  <canvas id="myChart1"></canvas>
</div>
    </div>
    <div class="carousel-item">
    <div class="chart_containers d-block w-100" style="position: relative; height:40vh; width:80vw">
  <canvas id="myChart"></canvas>
</div>
    </div>
    </center>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
          <!-- Tutaj umieść swoje wykresy -->
          


          
        </div>

        <div class="modal-footer">
          <button type="submit" onclick="generatePDF()" class="btn btn-danger" onclick='obrazek()' >Generuj</button>
        </div>

      </div>
    </div>
  </div>

         <script>



          var jsonData1 = <?php echo $jsonData1; ?>; // Przekazanie danych JSON do JavaScriptu
// Utwórz wykres
var ctx = document.getElementById('myChart1').getContext('2d');
const config = {
  type: 'line',
  data: jsonData1,
  options: {
    responsive: true,
    plugins: {
      title: {
        display: true,
        text: (ctx) => 'Ilość zrobienia',
      }
    }
  }
};

var myChart = new Chart(ctx, config);


var jsonData = '<?php echo $jsonData; ?>';
    
    var sumaIlosc = <?php echo $sumaIlosc; ?>;
    
    var sumaIloscZrealizowana = <?php echo $sumaIloscZrealizowana; ?>;
    var sumaKolumnyJeden = <?php echo $sumaKolumnyJeden; ?>;
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Ilość do zrobienia', 'Ilość zrealizowana', 'Ilość z przed rewizji'],
        datasets: [{
          label: 'Dane',
          data: [sumaIlosc-sumaIloscZrealizowana-sumaKolumnyJeden, sumaIloscZrealizowana-sumaKolumnyJeden,sumaKolumnyJeden],
          backgroundColor: ['rgba(54, 162, 235, 0.6)','rgba(255, 205, 86, 0.6)','rgba(255, 99, 132, 0.6)'],
          borderColor: ['rgba(54, 162, 235, 1)','rgba(255, 205, 86, 1)','rgba(255, 99, 132, 1)'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Stopień ukończenia'
          }
        },
        scales: {
          y: {
            max: sumaIlosc // Ustaw maksymalną wartość na sumę 'ilosc'
          }
        }
      }
    });
         </script>
<?php } ?>





<div class="modal fade" id="mymodal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edycja Detalu</h4>
      </div>
      <form method="POST" action="zapisze_dane.php" id="myForm">
        <div class="modal-body">
          Nazwa projektu: <label id="projectName" name="projectName"></label><br />
          <input type="hidden" name="project">
          Zespół: <label id="zespolName" name="zespolName"></label><br />
          Detal: <label id="detalName" name="detalName"></label><br />
          <input type="hidden" name="detal">
          Numer pracownika: <label id="numerName" name="numerName"></label>
          <input type="hidden" name="numer">
          <br />

          <?php if (!isUserParts()) { ?>
            <input class="form-control" type="number" inputmode="numeric" placeholder="Ilość" name="ilosc">
            <br />
            <input class="form-control" type="number" inputmode="numeric" placeholder="Długość" name="dlugosc">
            <br />

            <select class="form-control" name="maszyna" required>
              <option value="Recznie" selected>Recznie</option>
              <option value="Pila">Pila</option>
            </select>
          <?php } ?>
        </div>
        <div class="modal-footer">
          <?php
          if (isUserParts()) { ?>
            <button type="button" name="save" class="btn btn-default" value='usun' onclick="showConfirmation()">Kasuj Projekt</button>
          <?php }
          ?>
          <?php
          if (!isUserParts()) { ?>
            <button type="Submit" name="save" class="btn btn-default" value='piece'>Zapisz</button>
          <?php }
          ?>

        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>
</div>
<div class="modal" id="user-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Weryfikacja użytkownika</h5>
      </div>
      <div class="modal-body">
        <form id="user-form">
          <div class="form-group">
            <label for="user-number">Wprowadź swój numer:</label>
            <?php
            if (isUserPartsKier()) {
              $kiersql = "Select * from dbo.Persons where [user]=''";
              $stmt = sqlsrv_query($conn, $kiersql);
            ?> <select type="text" class="form-control" id="user-number" name="user-number" required>
                <?php
                while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                ?>
                  <option value="<?php echo $data['identyfikator'];  ?>"  data-imie-nazwisko="<?php echo $data['imie_nazwisko']; ?>"><?php echo $data['imie_nazwisko']; ?></option>

                <?php }
                ?>
              </select> <?php

                      } else if (!isUserPartsKier()) { ?>
              <input type="number" class="form-control" id="user-number" name="user-number">
            <?php } ?>
          </div>

          <div class="modal-footer">
            <?php
            if (isUserPartsKier()) { ?>
              <button id="submit-button" class="btn btn-default">Przejdź</button>
            <?php } else if (!isUserPartsKier()) { ?>
              <a href="..\index.php" class="btn btn-default">Strona główna</a>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php if (isUserPartsKier()) { ?>
<div id="myElement" class="bottom-banner1"></div>
<?php } ?>

</div>
</body>
<script src="../static/jspdf.min.js"></script>
<script src="../static/jspdf.plugin.autotable.min.js"></script>

<script src="../static/html2canvas.min.js"></script>
<script>

$(document).ready(function () {
        // Obsługa zdarzenia zmiany checkboxa
        $('#checkbox').change(function () {
            // Wyślij formularz po zaznaczeniu lub odznaczeniu checkboxa
            $('#myForm1').submit();
        });

        // Obsługa zdarzenia zmiany pola select
        $('#pageSizeSelect').change(function () {
            // Wyślij formularz po zmianie wartości w polu select
            $('#myForm1').submit();
        });
    });

$('html').addClass('js');


$(window).on("load", function() {
    $("#loader-wrapper").fadeOut();
});


function generatePDF() {
  var doc = new jsPDF({
    orientation: 'landscape'
  });

  doc.setFont('Helvetica');
  var tableTitleElement = document.getElementById('tableTitle');
    var tableTitle = tableTitleElement.innerText;

    tableTitle = tableTitle.replace(/<br\s*\/?>/gi, '\n');
  var table = document.getElementById('myTablereport');

  var tableData = doc.autoTableHtmlToJson(table);


  var pageWidth = doc.internal.pageSize.width;
  var textWidth = doc.getTextWidth(tableTitle);
  var textX = (pageWidth - textWidth) / 2;
  var textY = 20; // Ustal wartość Y, aby umieścić tekst na górze strony

  doc.setFontSize(18);
  doc.setFontStyle('bold');
  doc.text(tableTitle, textX, textY, { align: 'center' });

  doc.autoTable({
    head: [tableData.columns],
    body: tableData.data,
    startY: 30,
    margin: { top: 10, bottom: 10 },
    styles: {
      fontSize: 8 // Rozmiar czcionki
    }
  });

  doc.save('Raport.pdf');

}

var currentPage = <?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>;

const pageItems = document.querySelectorAll('.pagination li');

  // Iteracja przez każdy element li i usunięcie słowa "Current"
  pageItems.forEach(function(item) {
    if (item.classList.contains('active')) {
      item.querySelector('.page-link').innerHTML = currentPage;
    }
  });

  function showConfirmation() {
    var form = document.getElementById("myForm");
    var result = confirm("Czy na pewno chcesz usunąć projekt z danym ID?");
    if (result) {
      alert("Potwierdzono!");
      form.submit();
    } else {
      alert("Anulowano!");
    }
  }

  var clicks = 0;
  var timeout;

  function handleClick(row) {
    clicks++;

    if (clicks === 1) {
      timeout = setTimeout(function() {
        singleClickAction(row);
        clicks = 0;
      }, 200);
    } else if (clicks === 2) {
      clearTimeout(timeout);
      doubleClickAction(row);
      clicks = 0;
    }
  }

  var selectedrow = [];

  function singleClickAction(row) {
    var hasClass = row.classList.contains("table-warning");
    if (hasClass) {
      row.classList.remove("table-warning");
      removeRowFromSelected(getColumnData(row, "project") + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
    } else {
      row.classList.add("table-warning");
      addRowToSelected(getColumnData(row, "project") + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
    }
  }

  function addRowToSelected(row) {
    selectedrow.push(row);
  }

  function getColumnData(row, columnId) {
    var columnElement = row.querySelector('#' + columnId);
    return columnElement.innerText;
  }

  function removeRowFromSelected(row) {
    var index = selectedrow.indexOf(row);
    if (index !== -1) {
      selectedrow.splice(index, 1);
    }
  }

  function doubleClickAction(row) {
    var projectName = row.querySelector('#project').innerHTML;
    var zespolName = row.querySelector('#zespol').innerHTML;
    var detalName = row.querySelector('#detal').innerHTML;

    var projectNameDiv = document.querySelector('#mymodal #projectName');
    var zespolNameDiv = document.querySelector('#mymodal #zespolName');
    var detalNameDiv = document.querySelector('#mymodal #detalName');
    var numerNameDiv = document.querySelector('#mymodal #numerName');

    projectNameDiv.innerHTML = projectName;
    zespolNameDiv.innerHTML = zespolName;
    detalNameDiv.innerHTML = detalName;
    numerNameDiv.innerHTML = localStorage.getItem('number1');

    document.getElementById("myForm").elements.namedItem("project").setAttribute("value", projectName);
    document.getElementById("myForm").elements.namedItem("detal").setAttribute("value", detalName);
    document.getElementById("myForm").elements.namedItem("numer").setAttribute("value", localStorage.getItem('number1'));

    $('#mymodal').modal('show');
  }

  function sendSelectedRowsToPHP() {
    var xhr = new XMLHttpRequest();
    var url = 'zakoncz.php';
    var params = 'selectedrow=' + JSON.stringify(selectedrow);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Odpowiedź z serwera
        console.log(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(params);
  }

  function sendSelectedRowsToPHP1() {
    var xhr = new XMLHttpRequest();
    var url = 'zakoncz1.php';
    var params = 'selectedrow=' + JSON.stringify(selectedrow);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Odpowiedź z serwera
        console.log(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(params);
  }

  function sendSelectedRowsToPHP2() {
    var xhr = new XMLHttpRequest();
    var url = 'zakoncz2.php';
    var params = 'selectedrow=' + JSON.stringify(selectedrow);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Odpowiedź z serwera
        console.log(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(params);
  }

  function status() {
    var xhr = new XMLHttpRequest();
    var url = 'status.php';
    var params = 'selectedrow=' + JSON.stringify(selectedrow);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        // Odpowiedź z serwera
        console.log(xhr.responseText);
        location.reload();
      }
    };

    xhr.send(params);
  }
</script>
<?php if (!isUserPartskier() and !isLoggedIn()) { ?>
  <script>
    var stored = localStorage.getItem('number1');
if (stored !== null) {
  var colorButton = document.getElementById('time');
  var percent =  0;

  function changeColor() {
    percent += 0.1;
    colorButton.style.width = `${percent}%`;

    if (percent < 100) {
      setTimeout(changeColor, 200); // Powtórz co 1 sekundę (1000 milisekund)
      localStorage.setItem('czas', percent);
    } else {
      localStorage.removeItem('number1');
      localStorage.removeItem('czas');
      location.reload();
    }
  }

  changeColor(); // Wywołaj funkcję changeColor() po załadowaniu strony
}

    setTimeout(changeColor, 5000);

    setTimeout(changeColor, 1000); // Rozpocznij po 5 sekundach

    function sendcheck() {
      usernumber = document.getElementById('user-number');
      sendForm(userNumber);
    }

    
  </script>
<?php } ?>
<?php if (!isUserParts()) { ?>
  <script>
    var stored;
    var nazwa;
    $(document).ready(function() {
      stored = localStorage.getItem('number1');
      nazwa= localStorage.getItem('nazwa');
      if (stored) {
        // Numer został już poprawnie sprawdzony, nie wyświetlamy okna dialogowego
        console.log('Numer został już sprawdzony: ' + stored);
        toastr.success('Weryfikacja przebiegła pomyślnie!!!<br/> Witaj '+nazwa);
        try {
          document.getElementById('myElement').innerHTML = "Pracujesz w kontekście <br>"+nazwa;
} catch (error) {
  console.error();
}
        
      } else {
        // Numer nie został jeszcze sprawdzony, wyświetlamy okno dialogowe
        $('#user-modal').modal({
          backdrop: 'static',
          keyboard: false
        });
        $('#user-modal').modal('show');
        $('#user-number').focus();


        $('#user-modal').on('shown.bs.modal', function() {
          selectInput();
        });



        $('#user-number').on('input', function() {
          var userNumber = $(this).val();


          $('#user-number').on('keypress', function(e) {
            if (e.which === 13) {
              e.preventDefault();
              var userNumber = $(this).val();
              if (userNumber.length === 10) {
                sendForm(userNumber);
              } else {
                console.log('Wprowadź dokładnie 10 cyfr.');
              }
            }
          });
        });

        $('#submit-button').on('click', function(e) {
          e.preventDefault(); // Zapobiegamy domyślnemu zachowaniu przycisku (np. przeładowaniu strony)
          var userNumber = $('#user-number').val();
          if (userNumber.length === 10) {
            sendForm(userNumber);
          } else {
            console.log('Wprowadź dokładnie 10 cyfr.');
          }
        });

        function sendForm(userNumber) {
          $.ajax({
            url: 'check_person.php',
            type: 'POST',
            data: {
              number: userNumber
            },
            success: function(response) {
              var czesci = response.split(",")
              console.log(response);
              if (czesci[0] === 'true') {
                console.log('Twój numer znajduje się w bazie danych!');
                localStorage.setItem('number1', userNumber);
                localStorage.setItem('nazwa',czesci[1]);
                localStorage.setItem('czas', 0);
                location.reload();
              } else {
                console.log('Twój numer nie został odnaleziony w bazie danych.');
                location.reload();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log('Wystąpił błąd podczas sprawdzania numeru w bazie danych.');
              location.reload();
              console.log(jqXHR.responseText);
            },
            complete: function() {
              $('#user-modal').modal('hide');
            }
          });
        }
      }
    });
  </script>
<?php } ?>

</html>