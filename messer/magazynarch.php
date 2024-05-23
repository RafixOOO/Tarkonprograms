<?php
require_once("dbconnect.php");

$sql="SELECT
    m.PartID,
    MAX(m.[Date]) AS data,
    (
        SELECT STRING_AGG( Person, ', ')
        FROM (
            SELECT DISTINCT Person
            FROM PartCheck.dbo.MagazynExtra
            WHERE PartID = m.PartID
                AND Localization = m.Localization
                AND Deleted = 0
        ) AS distinct_persons
    ) AS Persons,
    m.Localization,
    (SELECT COUNT(l.PartID) from PartCheck.dbo.MagazynExtra l where l.PartID=m.PartID and l.Localization=m.Localization ) AS Ilosc,
    (SELECT COUNT(h.SheetName) from SNDBASE_PROD.dbo.StockArchive h where h.SheetName=sh1.SheetName) as zuzyte,
    sh1.Material,
    sh1.Thickness,
    sh1.[Length],
    sh1.Width
FROM
    PartCheck.dbo.MagazynExtra m
LEFT JOIN
    SNDBASE_PROD.dbo.StockArchive sh1 on m.PartID=sh1.SheetName COLLATE SQL_Latin1_General_CP1_CI_AS
WHERE m.Deleted = 1 or EXISTS (
        SELECT 1
        FROM
            SNDBASE_PROD.dbo.StockArchive sh
        WHERE
            sh.SheetName = m.PartID COLLATE SQL_Latin1_General_CP1_CI_AS
        AND sh1.Qty = 0
    )
GROUP BY
    m.PartID, m.Localization, sh1.Material,
    sh1.Thickness,
    sh1.[Length],
    sh1.Width, sh1.SheetName, m.Deleted
ORDER BY
    MAX(m.[Date]) DESC;";
$datas = sqlsrv_query($conn, $sql);
if ($datas === false) {
    die(print_r(sqlsrv_errors(), true)); // Error handling
}
?>
<html>
<head>

    <?php include 'globalhead.php'; ?>
<style>
.dataTables_filter {
    float: right; /* Przesunięcie pola wyszukiwania na prawo */
    margin-right: 20px; /* Odstęp między polem wyszukiwania a prawym brzegiem tabeli */
}
</style>
</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php include 'globalnav.php'; ?>
<div class="container-xxl">

<div class="table-responsive">
<?php
echo "<table class='table table-sm table-hover table-bordered' id='mytable'
                style='font-size: calc(9px + 0.390625vw)'>";
echo "<thead>";
echo "<tr><tr>
        <th>Sheetname</th>
        <th>Date</th>
        <th>Person</th>
        <th>Localization</th>
        <th>Used</th>
        <th>Material</th>
        <th>Thickness</th>
        <th>Length</th>
        <th>Width</th>
";
echo "  </tr></tr>";
echo "</thead>";
echo "<tbody>";
while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
    echo "<tr id='main' class='clickable-row' data-partid='".$row['PartID']."'>";
    echo "<td>".$row['PartID']."</td>";
    echo "<td>".$row['data']->format('Y-m-d H:i:s')."</td>"; // Zakładając, że Date jest typu datetime
    echo "<td>".$row['Persons']."</td>";
    echo "<td>".(($row['Localization'] == 16) ? "kooperacja" : (($row['Localization'] == 17) ? "zewnątrz" : $row['Localization']))."</td>";
    echo "<td>".$row['zuzyte']."</td>";
    echo "<td>".$row['Material']."</td>";
    echo "<td>".$row['Thickness']."</td>";
    echo "<td>".$row['Length']."</td>";
    echo "<td>".$row['Width']."</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
?>
</div>
</div>
</body>
    <script src="../static/jquery-3.7.0.js"></script>
    <script src="../static/jquery.dataTables.min.js"></script>
    <script src="../static/dataTables.bootstrap5.min.js"></script>
<script>
        $(document).ready(function(){
            $('#mytable').DataTable({
                paging: false, // Wyłączenie paginacji
                info: false // Wyłączenie informacji o liczbie rekordów
            });
        });
</script>

</html>