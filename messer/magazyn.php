<?php
require_once("dbconnect.php");

$sql="SELECT
    m.PartID,
    MAX(m.[Date]) AS data,
    m.Person,
    m.Localization,
    (SELECT COUNT(l.PartID) from PartCheck.dbo.MagazynExtra l where l.PartID=m.PartID and l.Localization=m.Localization) AS Ilosc,
    (SELECT COUNT(h.SheetName) from SNDBASE_PROD.dbo.StockArchive h where h.SheetName=sh1.SheetName) as zuzyte,
    s.Material,
    s.Thickness,
    s.[Length],
    s.Width
FROM
    PartCheck.dbo.MagazynExtra m
LEFT JOIN
    SNDBASE_PROD.dbo.Stock s ON m.PartID = s.SheetName COLLATE SQL_Latin1_General_CP1_CI_AS
LEFT JOIN
    SNDBASE_PROD.dbo.StockArchive sh1 on m.PartID=sh1.SheetName COLLATE SQL_Latin1_General_CP1_CI_AS
WHERE NOT EXISTS (
        SELECT 1
        FROM
            SNDBASE_PROD.dbo.StockArchive sh
        WHERE
            sh.SheetName = m.PartID COLLATE SQL_Latin1_General_CP1_CI_AS
            and sh.Qty=0
    ) and Deleted = 0
GROUP BY
    m.PartID, m.Person, m.Localization, s.Material, s.Thickness, s.[Length], s.Width, sh1.SheetName
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
</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php include 'globalnav.php'; ?>
<div class="container-xxl">
<div class="mb-3" style="float:right;">
<div class="input-group">
<input type="text"  class="form-control" id="searchInput" placeholder="Nazwa arkusza...">
</div>
</div>
<div class="clearfix"></div>
<br />
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
        <th>Count</th>
        <th>Used</th>
        <th>Material</th>
        <th>Thickness</th>
        <th>Length</th>
        <th>Width</th>
    </tr></tr>";
echo "</thead>";
echo "<tbody>";
while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
    echo "<tr id='main' class='clickable-row' data-partid='".$row['PartID']."'>";
    echo "<td>".$row['PartID']."</td>";
    echo "<td>".$row['data']->format('Y-m-d H:i:s')."</td>"; // Zakładając, że Date jest typu datetime
    echo "<td>".$row['Person']."</td>";
    echo "<td>".(($row['Localization'] == 16) ? "kooperacja" : (($row['Localization'] == 17) ? "zewnątrz" : $row['Localization']))."</td>";
    echo "<td>".$row['Ilosc']."</td>";
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$(document).ready(function(){
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        if (value === '') {
            $("#mytable tbody tr#main").show(); // Pokaż wszystkie wiersze, gdy pole wyszukiwania jest puste
        } else {
            $("#mytable tbody tr:visible").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }
    });
});

$(document).ready(function() {
    $(".clickable-row").click(function() {
        var partID = $(this).data('partid');
        $(".details-row").hide().removeClass('visible-details'); // Ukryj wszystkie rzędy szczegółów i usuń klasę 'visible-details'
        $(".details-row-"+partID).toggle().addClass('visible-details'); // Pokaż lub ukryj rzęd szczegółów dla klikniętego PartID i dodaj klasę 'visible-details'
    });
});


</script>

</html>