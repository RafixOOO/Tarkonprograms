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
and cr_deleted!=true and cr_allow_work_time_registering=true and ( cr_end_date is null or cr_end_date>CURRENT_DATE);";
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cr_numbers = array_column($results, 'cr_number');
$cr_numbers_list = implode(",", array_map(function ($num) use ($pdo) {
    return $pdo->quote($num);
}, $cr_numbers));



$sqlother = "SELECT
p.[Projekt] AS ProjectName,
SUM(p.[Ilosc]) as ilosc,
SUM(r.[Ilosc_zrealizowana]) as ilosc_zrealizowana
FROM
[PartCheck].dbo.Parts p
LEFT JOIN
[PartCheck].dbo.Product_Recznie r ON p.[Pozycja] = r.[Pozycja]
LEFT JOIN [PartCheck].[dbo].[Product_V200] as v ON v.[Name]=p.[Pozycja] COLLATE Latin1_General_CS_AS
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
and p.[Pozycja]!=''
and p.[Projekt] IN ($cr_numbers_list)
GROUP BY
p.[Projekt]";

$datas1 = sqlsrv_query($conn, $sqlother);

?>
<!DOCTYPE html>
<html>

<head lang="PL">
    <?php
    require_once('globalhead.php');
    ?>
    <meta charset ="utf-8" />
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
    <!-- 2024 Created by: Rafał Pezda-->
    <!-- link: https://github.com/RafixOOO -->
    <div class="container mt-5">
        <br />
        <div class="row">
            <?php
            while ($row = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
            ?>
                <div class="col-xl-4 col-lg-4" style="height:800px;">
                    <a href="receiver.php?project_name=<?php echo $row['ProjectName']; ?>">
                        <div class="card l-bg-cherry">
                            <div class="card-statistic-3 p-4">
                                <div class="mb-4">
                                    <h5 class="card-title mb-0"><?php echo $row['ProjectName']; ?></h5>
                                </div>
                                <div class="row align-items-center mb-2 d-flex">
                                    <div class="col-8">
                                        <h2 class="d-flex align-items-center mb-0">
                                            <?php echo number_format($row['ilosc_zrealizowana'] / $row['ilosc'], 2);; ?>%
                                        </h2>
                                    </div>
                                    <div class="col-4 text-right">
                                        <span>Liczba detali: <?php echo $row['ilosc']; ?></span>
                                    </div>
                                </div>
                                <div class="progress mt-1 " data-height="8" style="height: 8px;">
                                    <div class="progress-bar l-bg-cyan" role="progressbar" data-width="25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo number_format($row['ilosc_zrealizowana'] / $row['ilosc'], 2);; ?>%;"></div>
                                </div>
                                <div >
                                    <br />
                                <?php $godziny = "SELECT     CAST(ROUND(sum_cuce_quantity, 0) AS INT) AS sum_cuce_quantity, czynnosc, cr_number
FROM PartCheck.dbo.hrappka_godziny where cr_number='$row[ProjectName]';";
$datas2 = sqlsrv_query($conn, $godziny);
$razem=0;
echo "<table style='width:100%;'>";
    echo "<tr><th>Czynność</th><th style='padding-left: 20px;'>Godziny</th></tr>";
    while ($row = sqlsrv_fetch_array($datas2, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['czynnosc'] . "</td>";
        echo "<td>" . $row['sum_cuce_quantity'] . "</td>";
        $razem=$razem+$row['sum_cuce_quantity'];
        echo "</tr>";
    }
    echo "<tfoot>";
    echo "<td><b>Razem</b></td>";
    echo "<td><b>" . $razem . "</b></td>";
    echo "</tfoot>";
    echo "</table>";
?>
</div>
                            </div>
                        </div>
                    </a>
                </div>
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

</html>