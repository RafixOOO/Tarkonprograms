<?php

require_once("dbconnect.php");


$myVariable = isset($_GET['toggleButtons']) ? $_GET['toggleButtons'] : "Projekt";
$projekt = isset($_GET['keywords']) ? $_GET['keywords'] : '';
if ($projekt == '') {
    $pro = '+_+';
} else {
    $pro = strtoupper($projekt);
}

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
where p.[Pozycja] !='' and p.[" . $myVariable . "] LIKE '%$pro%' and p.lock is NULL
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
where p.[Pozycja] !='' and p.[" . $myVariable . "] LIKE '%$pro%' and p.lock is NULL
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
) and p.[Pozycja] !='' and p.[" . $myVariable . "] LIKE '%$pro%' and p.lock is NULL
group by p.[Pozycja],p.[Projekt], p.[Status], b.[Pozycja],p.Zespol,p.[Ilosc]";
$datasrecznie = sqlsrv_query($conn, $sqlrecznie);


$sqlproject = "SELECT Distinct
(select max(p1.[Id])
from [PartCheck].[dbo].[Parts] p1
where p.[Zespol]=p1.Zespol and p1.[Pozycja] !='' and p1.[" . $myVariable . "] LIKE '%$pro%') as id,
p.[Projekt] as ProjectName,
p.[Zespol] AS zespol
,(select p1.[Ilosc]
from [PartCheck].[dbo].[Parts] p1
where p.[Zespol]=p1.Zespol and p1.[Pozycja] ='') as ilosc
from [PartCheck].[dbo].[Parts] as p
where p.[Pozycja] !='' and p.[" . $myVariable . "] LIKE '%$pro%'
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
    require_once('globalhead.php');
    require_once('../auth.php');
    ?>
    <style>
        .green {
            background-color: #daecd1;
        }

        .yellow {
            background-color: #fff06ee8;
        }

          /* Styl dla spinnera */
  #loadingIndicator {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* Wycentruj spinner */
    z-index: 10000; /* Zadaj jeszcze wyższy indeks warstwy, aby spinner był na wierzchu */
    display: none; /* Ukryj początkowo spinner */
}

/* Dodatkowe style dla większego spinnera */
.spinner-border {
    width: 6rem; /* Szerokość spinnera */
    height: 6rem; /* Wysokość spinnera */
    border-width: 0.5em; /* Grubość obramowania */
}
    </style>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
<?php require_once('globalnav.php'); ?>
<div class="container-xl">
    <form method="get" action="">
        <div class="input-group" style="font-size: 150%;">
            <input type="text" class="form-control" name="keywords" oninput="convertToUppercase(this)"
              placeholder="<?php echo !empty($projekt) ? $projekt : 'Nazwa...'; ?>" style="font-size: 150%;" autofocus>
            <button class="btn btn-primary" type="submit" style="font-size: 150%;">Szukaj</button>
        </div>
      <div id="toggleButtons" class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary btn-lg <?php if ($myVariable == 'Zespol') echo 'active'; ?>">
          <input type="radio" name="toggleButtons" id="assemblyRadio" value="Zespol" autocomplete="off" <?php if ($myVariable == 'Zespol') echo 'checked'; ?>> Zespół
        </label>
        <label class="btn btn-secondary btn-lg  <?php if ($myVariable == 'Pozycja') echo 'active'; ?>">
          <input type="radio" name="toggleButtons" id="positionRadio" value="Pozycja" autocomplete="off" <?php if ($myVariable == 'Pozycja') echo 'checked'; ?>> Detale
        </label>
        <label class="btn btn-secondary btn-lg  <?php if ($myVariable == 'Projekt') echo 'active'; ?>">
          <input type="radio" name="toggleButtons" id="projectRadio" value="Projekt" autocomplete="off" <?php if ($myVariable == 'Projekt') echo 'checked'; ?>> Projekt
        </label>
      </div>


    </form>
</div>
<div class="container mt-5">
    <div id="loadingIndicator" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <br/>
    <div class="row" id="masonry-grid">
        <?php while ($data = sqlsrv_fetch_array($datasproject, SQLSRV_FETCH_ASSOC)) :
            $orange = 0;
            $dark = 0;
            ?>

            <div class="col-md-2"> <!-- Ustawiamy szerokość karty na 4 kolumny na ekranach większych niż "md" -->
                <div id="<?php echo 'collapse' . $data['id']; ?>" class="card mb-3">
                    <div class="card-header">
                        <span><?php echo $data['zespol'] ?></span>
                        <span class="float-end"><?php echo $data['ilosc']; ?></span>
                    </div>
                    <div class="card-body">

                        <p class="card-text">
                            <?php foreach ($dataresult1

                            as $data1) : ?>
                            <?php if ($data['zespol'] == $data1['zespol']) : ?>
                            <?php if ($data1['ilosc_full'] <= $data1['ilosc_zrealizowana'] and $data1['ilosc_zrealizowana'] != '') : ?>
                        <div class="text-success">
                            <div>
                                <span title="Części są w pełni zakończone"><?php echo $data1['Detal']; ?></span>
                                <span class="float-end"
                                      title="<?php echo "Aktualnie zrobione: " . $data1['ilosc_zrealizowana']; ?>"><?php echo $data1['ilosc']; ?></span>
                            </div>
                        </div>
                        <?php elseif ($data1['ilosc'] <= $data1['ilosc_zrealizowana'] and $data1['ilosc_zrealizowana'] != '') :
                            $orange = $orange + 1;
                            ?>
                            <div style="color:#a88102">
                                <div><a style="color:#a88102"
                                        title="Cześci pasują do kilku Assembly i nie są w pełni zakończone"><?php echo $data1['Detal']; ?></a></span>
                                    <span class="float-end"
                                          title="<?php echo "Aktualnie zrobione: " . $data1['ilosc_zrealizowana']; ?>"><?php echo $data1['ilosc']; ?>
                                </div>
                            </div>
                        <?php else :
                            $dark = $dark + 1;
                            ?>
                            <div>
                                <div><a class='text-dark'><?php echo $data1['Detal']; ?></a></span>
                                    <span class="float-end"
                                          title="<?php echo "Aktualnie zrobione: " . $data1['ilosc_zrealizowana']; ?>"><?php echo $data1['ilosc']; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        </p>
                        <?php
                        if ($dark == 0 and $orange == 0) {
                            $id = 'collapse' . $data['id'];
                            echo "<script>";
                            echo "var row6 = document.getElementById('" . $id . "');";
                            echo "if (row6) {";
                            echo "  row6.classList.add('green');";
                            echo "}";
                            echo "</script>";
                        } elseif ($orange >= 1 and $dark == 0) {
                            $id = 'collapse' . $data['id'];
                            echo "<script>";
                            echo "var row6 = document.getElementById('" . $id . "');";
                            echo "if (row6) {";
                            echo "  row6.classList.add('yellow');";
                            echo "}";
                            echo "</script>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>


</div>
</body>
<script src="../static/masonry.pkgd.min.js"></script>
<script>

function showLoadingIndicator() {
    document.getElementById('loadingIndicator').style.display = 'block';
}

// Dodanie nasłuchiwacza zdarzeń do linków paginacji
var paginationLinks = document.querySelectorAll('a');
paginationLinks.forEach(function(link) {
        link.addEventListener('click', showLoadingIndicator);
});
    function convertToUppercase(inputElement) {
        inputElement.value = inputElement.value.toUpperCase();
    }

    var masonryGrid = new Masonry('#masonry-grid', {
        gutter: 47
    });
</script>

</html>