<?php require_once '../auth.php'; ?>

<?php

require_once("dbconnect.php");

try {
    $dsn = "pgsql:host=10.100.100.42;port=5432;dbname=hrappka;";
    $username = "hrappka";
    $password = "1UjJ7DIHXO3YpePh";

    // Utworzenie instancji PDO
    $pdo = new PDO($dsn, $username, $password);

    // Ustawienie opcji PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
$sql = "SELECT *
FROM public.company_contractor_requests
inner join public.tags t on cr_id=t.tag_entity_fkey and t.tag_body = 'RCP'
where (cr_state=
'Aktywny' or  cr_state= 'Produkcja-aktywny')
and cr_deleted!=true and cr_allow_work_time_registering=true and ( cr_end_date is null or cr_end_date>CURRENT_DATE)
order by cr_number desc";
$stmt = $pdo->query($sql);


?>
<!DOCTYPE html>
<html>

<head lang="PL">
    <?php
    require_once('globalhead.php');
    ?>
    <meta charset="utf-8" />
    <style>
        .verticalrotate {
            position: fixed;
            bottom: 50%;
            left: 84.5%;
            width: 30%;
            transform: rotate(-90deg);
        }
    </style>
    <?php if(!isLoggedIn()){ ?>
    <script>
        // Sprawdzenie localStorage przed załadowaniem strony
        const number1 = localStorage.getItem('number1');
        if (!number1) {
            // Jeśli dane są puste, przekierowanie na inną stronę
            window.location.href = 'panel.php'; // Podaj adres strony błędu lub logowania
        }
    </script>
    <?php } ?>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
<?php if (!isLoggedIn()) { ?>
            <div class="progress verticalrotate">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
            </div>
        <?php } ?>
<?php if(isLoggedIn()){ ?>
<?php require_once("navbar.php"); ?>
<br /><br /><br /><br />
<?php } ?>
    <!-- 2024 Created by: Rafał Pezda-->
    <!-- link: https://github.com/RafixOOO -->
    <?php if (isLoggedIn()) { ?>
    <?php if(isSidebar()==0){ ?>
        <div class="container-fluid" style="width:80%;margin-left:16%;">
    <?php }else if(isSidebar()==1){ ?>
        <div class="container-fluid" style="width:90%; margin: 0 auto;">
        <?php } ?>
    <?php } else { ?>

      <div class="container-fluid" style="margin-left:auto;margin-right:auto;">

      <?php } ?>
        <br />
        <div class="row">
            <?php
            while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Tutaj możesz wykonywać operacje na każdym wierszu
            ?>
                <div class="col-xl-4 col-lg-4">
                    <a href="receiver.php?project_name=<?php echo $row1['cr_number']; ?>">
                        <div class="card l-bg-cherry">
                            <div class="card-statistic-3 p-4">
                                <div class="mb-4">
                                    <h5 class="card-title mb-0"><?php echo $row1['cr_number']; ?></h5>
                                </div>

                                <?php

                                $sqlother = "WITH CombinedData AS (
    SELECT
        c.PROJEKT AS ProjectName,
        c.CNT AS ilosc,
        SUM(r.[Ilosc_zrealizowana]) AS ilosc_zrealizowana
    FROM
    [PartCheck].dbo.cutlogic c
    left JOIN
        [PartCheck].dbo.Product_Recznie r ON c.CZESC = r.[Pozycja]
    WHERE
         c.PROJEKT != ''
        and c.PROJEKT = '$row1[cr_number]'
    GROUP BY
        c.PROJEKT,c.CNT
    UNION ALL
    SELECT 
        ProjectName AS ProjectName,
        AmountNeeded AS ilosc, 
        AmountDone AS ilosc_zrealizowana
    FROM 
        PartCheck.dbo.Product_V630
    WHERE 
        Name != ''
        and ProjectName = '$row1[cr_number]' 
    UNION ALL
    -- Trzecie zapytanie
    SELECT 
        ISNULL(p.[WoNumber], m1.[WoNumber]) AS ProjectName,
        ISNULL(p.[QtyOrdered], m1.[QtyOrdered]) AS ilosc,
        ISNULL(p.[QtyCompleted], m1.[QtyProgram]) AS ilosc_zrealizowana
    FROM [PartCheck].[dbo].[PartArchive_Messer] m1
right join [SNDBASE_PROD].[dbo].[Part] p on m1.PartName=p.PartName
    WHERE 
        (p.[PartName] != '' or m1.[PartName] != '')
        and (p.[WoNumber] = '$row1[cr_number]' or m1.[WoNumber] = '$row1[cr_number]')
    GROUP BY 
        p.[WoNumber], m1.[WoNumber],p.[QtyOrdered], m1.[QtyOrdered],p.[QtyCompleted], m1.[QtyProgram]
)
SELECT 
    SUM(ilosc) AS ilosc,
    SUM(ilosc_zrealizowana) AS ilosc_zrealizowana
FROM 
    CombinedData;
";

                                $datas1 = sqlsrv_query($conn, $sqlother);

                                while ($row = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
                                ?>

                                    <div class="row align-items-center mb-2 d-flex">
                                        <div class="col-8">
                                            <h2 class="d-flex align-items-center mb-0">
                                                <?php
                                                // Sprawdzenie, czy 'ilosc' nie jest zerem, aby uniknąć dzielenia przez zero
                                                if ($row['ilosc'] != 0) {
                                                    // Oblicz procent
                                                    $percentage = ($row['ilosc_zrealizowana'] / $row['ilosc']) * 100;
                                                    // Wyświetl procent z dwoma miejscami po przecinku
                                                    echo number_format($percentage, 2) . '%';
                                                } else {
                                                    // Obsłuż przypadek, gdy 'ilosc' jest zerem
                                                    echo '0.00%';
                                                }
                                                ?>
                                            </h2>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span>Liczba detali: <?php echo $row['ilosc']; ?></span>
                                        </div>
                                    </div>
                                    <div class="progress mt-1 " data-height="8" style="height: 8px;">
                                        <div class="progress-bar l-bg-cyan" role="progressbar" data-width="25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: <?php
// Sprawdzenie, czy 'ilosc' nie jest zerem, aby uniknąć dzielenia przez zero
if ($row['ilosc'] != 0) {
    // Oblicz procent
    $percentage = ($row['ilosc_zrealizowana'] / $row['ilosc']) * 100;
    // Wyświetl procent z dwoma miejscami po przecinku
    echo number_format($percentage, 2) . '%;';
} else {
    // Obsłuż przypadek, gdy 'ilosc' jest zerem
    echo '0%;';
}
?>"></div>
                                    </div>
                                <?php } ?>
                                <?php if(isLoggedIn()) { ?>
                                <div>
                                    <br />
                                    <?php
                                    $godziny = "SELECT  CAST(ROUND(sum_cuce_quantity, 0) AS INT) AS sum_cuce_quantity, czynnosc, cr_number
FROM PartCheck.dbo.hrappka_godziny where cr_number='$row1[cr_number]';";
                                    $datas2 = sqlsrv_query($conn, $godziny);
                                    $razem = 0;
                                    echo "<table style='width:100%;'>";
                                    echo "<tr><th>Czynność</th><th style='padding-left: 20px;'>Godziny</th></tr>";
                                    while ($row = sqlsrv_fetch_array($datas2, SQLSRV_FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['czynnosc'] . "</td>";
                                        echo "<td>" . $row['sum_cuce_quantity'] . "</td>";
                                        $razem = $razem + $row['sum_cuce_quantity'];
                                        echo "</tr>";
                                    }
                                    echo "<tfoot>";
                                    echo "<td><b>Razem</b></td>";
                                    echo "<td><b>" . $razem . "</b></td>";
                                    echo "</tfoot>";
                                    echo "</table>";
                                    echo "</div>";
                                    ?>
<?php } ?>
                                </div>
                                
                            </div>
                    </a>
                </div>
            <?php  } ?>
            <?php if (!isUserParts()) { ?>
                        <?php if (!isUserPartsKier()) { ?>
                            <div class="btn-toolbar position-fixed" role="toolbar" aria-label="Toolbar with button groups" style="bottom:4%;">
                <div class="btn-group me-2 " role="group" aria-label="First group"></div>
                            <button type="button" onclick="localStorage.removeItem('number1'); window.location.href = 'panel.php';" class="btn btn-warning btn-lg">Wyjdź
                            </button>
                            </div></div>
                        <?php } ?>
                        <?php } ?>
        </div>
    </div>


    </div>
    <?php if (!isLoggedIn()) { ?>
        <link rel="stylesheet" href="../assets/css/plugins.min.css" />
        <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
        <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
        <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
        <script src="../assets/js/core/popper.min.js"></script>
        <script src="../assets/js/core/bootstrap.min.js"></script>

        <!-- jQuery Scrollbar -->
        <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

        <!-- jQuery Sparkline -->
        <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

        <!-- Kaiadmin JS -->
        <script src="../assets/js/kaiadmin.min.js"></script>
    <?php } ?>
    <?php if (isLoggedIn()) { ?>

        <?php require_once('globalnav.php'); ?>
    <?php } ?>
</body>
<?php if (!isUserPartskier() and !isLoggedIn()) { ?>
    <script>
        var stored = localStorage.getItem('number1');
        if (stored !== null) {
            var colorButton = document.getElementById('time');
            var percent = 0;

            function changeColor() {
                percent += 0.1;
                colorButton.style.width = `${percent}%`;

                if (percent < 100) {
                    setTimeout(changeColor, 200); // Powtórz co 1 sekundę (1000 milisekund)
                    localStorage.setItem('czas', percent);
                } else {
                    localStorage.removeItem('number1');
                    localStorage.removeItem('czas');
                    window.location.href = 'panel.php';
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

</html>