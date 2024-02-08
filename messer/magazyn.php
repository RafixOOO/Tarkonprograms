<?php
require_once("dbconnect.php");

$sql="SELECT m.PartID,
       m.[Date] as data,
       m2.Person,
       m3.Localization,
       s.Material,
       s.Thickness,
       s.[Length],
       s.Qty,
       s.Width 
FROM (
    SELECT PartID, MAX([Date]) as max_date
    FROM PartCheck.dbo.MagazynExtra
    GROUP BY PartID
) max_dates
INNER JOIN PartCheck.dbo.MagazynExtra m ON max_dates.PartID = m.PartID AND max_dates.max_date = m.[Date]
inner JOIN SNDBASE_PROD.dbo.Stock s ON m.PartID = s.SheetName COLLATE SQL_Latin1_General_CP1_CI_AS
inner JOIN PartCheck.dbo.MagazynExtra m2 ON m.MagazynID = m2.MagazynID AND m2.[Date] = max_dates.max_date
inner JOIN PartCheck.dbo.MagazynExtra m3 ON m.MagazynID = m3.MagazynID AND m3.[Date] = max_dates.max_date;";
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
echo "<table class='table table-sm table-hover table-striped table-bordered' id='mytable'
                   style='font-size: calc(9px + 0.390625vw)'>";
echo "<thead>";
echo "<tr><th>Sheetname</th><th>Date</th><th>Person</th><th>Localization</th><th>Material</th><th>Thickness</th><th>Length</th><th>Qty</th><th>Width</th></tr>";
echo "</thead>";
echo "<tbody>";
while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
    echo "<tr id='main' class='clickable-row' data-partid='".$row['PartID']."'>";
    echo "<td>".$row['PartID']."</td>";
    echo "<td>".$row['data']->format('Y-m-d H:i:s')."</td>"; // Zakładając, że Date jest typu datetime
    echo "<td>".$row['Person']."</td>";
    echo "<td>".$row['Localization']."</td>";
    echo "<td>".$row['Material']."</td>";
    echo "<td>".$row['Thickness']."</td>";
    echo "<td>".$row['Length']."</td>";
    echo "<td>".$row['Qty']."</td>";
    echo "<td>".$row['Width']."</td>";
    echo "</tr>";

    // Początek drugiego while
    $othersql = "SELECT m.PartID,
                        m.[Date],
                        m.Person,
                        m.Localization  AS Localization,
                        s.Material,
                        s.Thickness,
                        s.[Length],
                        s.Qty,
                        s.Width 
                 FROM PartCheck.dbo.MagazynExtra m
                 LEFT JOIN SNDBASE_PROD.dbo.Stock s ON m.PartID = s.SheetName COLLATE SQL_Latin1_General_CP1_CI_AS
                 WHERE m.PartID='".$row['PartID']."'
                 order by m.[Date] desc";

    $otherdatas = sqlsrv_query($conn, $othersql);
    while ($otherrow = sqlsrv_fetch_array($otherdatas, SQLSRV_FETCH_ASSOC)) {
        echo "<tr class='details-row details-row-".$row['PartID']."' style='display: none;'>"; // Ukryj rzędy szczegółów na początku
        echo "<td></td>"; // Pusta komórka dla identyfikatora
        echo "<td></td>";
        echo "<td>".$otherrow['PartID']."</td>";
        echo "<td>".$otherrow['Date']->format('Y-m-d H:i:s')."</td>"; // Zakładając, że Date jest typu datetime
        echo "<td>".$otherrow['Person']."</td>";
        echo "<td>".$otherrow['Localization']."</td>";
        echo "</tr>";
    }
    // Koniec drugiego while
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