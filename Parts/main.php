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
where cr_state=
'Aktywny'
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
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
    <!-- 2024 Created by: Rafał Pezda-->
    <!-- link: https://github.com/RafixOOO -->
    <div class="container mt-5">
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
        p.[Projekt] AS ProjectName,
        SUM(p.[Ilosc]) AS ilosc,
        SUM(r.[Ilosc_zrealizowana]) AS ilosc_zrealizowana
    FROM
        [PartCheck].dbo.Parts p
    FULL JOIN
        [PartCheck].dbo.Product_Recznie r ON p.[Pozycja] = r.[Pozycja]
    LEFT JOIN 
        [PartCheck].[dbo].[Product_V200] AS v ON v.[Name] = p.[Pozycja] COLLATE Latin1_General_CS_AS
    WHERE
        NOT EXISTS (
            SELECT 1
            FROM [PartCheck].dbo.PartArchive_Messer m
            WHERE p.Pozycja = m.PartName COLLATE Latin1_General_CS_AS
        )
        AND NOT EXISTS (
            SELECT 1
            FROM [PartCheck].dbo.Product_V630 v
            WHERE p.Pozycja = v.Name
        )
        AND p.[Pozycja] != ''
        and p.[Projekt] = '$row1[cr_number]'
    GROUP BY
        p.[Projekt]
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
        [WoNumber] AS ProjectName,
        SUM([QtyOrdered]) AS ilosc,
        SUM([QtyProgram]) AS ilosc_zrealizowana
    FROM 
        [PartCheck].[dbo].[PartArchive_Messer]
    WHERE 
        [PartName] != ''
        and [WoNumber] = '$row1[cr_number]'
    GROUP BY 
        [WoNumber]
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

                                </div>
                            </div>
                    </a>
                </div>
            <?php  } ?>
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

</html>