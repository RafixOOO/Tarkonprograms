<!DOCTYPE html>

<?php
require_once("dbconnect.php");
require_once '../auth.php'; 
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
    (SELECT COUNT(l.PartID) from PartCheck.dbo.MagazynExtra l where l.PartID=m.PartID and l.Localization=m.Localization and l.Deleted=0) AS Ilosc,
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
WHERE m.Deleted = 0 and NOT EXISTS (
        SELECT 1
        FROM
            SNDBASE_PROD.dbo.StockArchive sh
        WHERE
            sh.SheetName = m.PartID COLLATE SQL_Latin1_General_CP1_CI_AS
            and sh1.Qty=0
    )
GROUP BY
    m.PartID, m.Localization, s.Material, s.Thickness, s.[Length], s.Width, sh1.SheetName, m.Deleted
ORDER BY
    MAX(m.[Date]) DESC;";
 $datas = sqlsrv_query($conn, $sql);
 if ($datas === false) {
     die(print_r(sqlsrv_errors(), true)); // Error handling
 }
?>
<html>
<?php include 'globalhead.php'; ?>
<head>

<style>
.dataTables_filter {
    float: right; /* Przesunięcie pola wyszukiwania na prawo */
    margin-right: 20px; /* Odstęp między polem wyszukiwania a prawym brzegiem tabeli */
}
</style>
</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<div class="container-fluid" style="width:80%;margin-left:auto;margin-right:auto;">
<ul class="nav nav-pills nav-primary" style="margin-left:auto;margin-right:auto;">
                      <li class="nav-item">
                        <a class="nav-link" href="main.php">Programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="wykonane.php">Zakończone programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" href="magazyn.php">Magazyn</a>
                      </li>
                    </ul>
<div class="table-responsive">
<?php
echo "<table class='table table-sm table-hover table-bordered' id='mytable'
                   style='font-size: calc(9px + 0.390625vw)'>";
echo "<thead>";
echo "<tr><tr>
        <th>Nazwa arkusza</th>
        <th>data</th>
        <th>Osoba</th>
        <th>Lokalizacja</th>
        <th>liczba</th>
        <th>użyte</th>
        <th>materiał</th>
        <th>grubość</th>
        <th>długość</th>
        <th>Wiszerokośćdth</th>
";
if (isUserMesser()) {
    echo "<th>opcje</th>";
}
  echo "  </tr></tr>";
echo "</thead>";
echo "<tbody>";
while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
    echo "<tr id='main' class='clickable-row' data-partid='".$row['PartID']."'>";
    echo "<td>".$row['PartID']."</td>";
    echo "<td>".$row['data']->format('Y-m-d H:i:s')."</td>"; // Zakładając, że Date jest typu datetime
    echo "<td>".$row['Persons']."</td>";
    echo "<td>".(($row['Localization'] == 16) ? "kooperacja" : (($row['Localization'] == 17) ? "zewnątrz" : $row['Localization']))."</td>";
    echo "<td>".$row['Ilosc']."</td>";
    echo "<td>".$row['zuzyte']."</td>";
    echo "<td>".$row['Material']."</td>";
    echo "<td>".$row['Thickness']."</td>";
    echo "<td>".$row['Length']."</td>";
    echo "<td>".$row['Width']."</td>";
     if (isUserMesser()) {
        echo "<td> <Button class='btn btn-primary btn-sm'>Usuń</Button></td>";
    }
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
?>
</div>
</div>
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Usuwanie produktu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>PartID: <span id="modal-partid"></span></p>
                <p>Lokalizacja: <span id="modal-localization"></span></p>
                <p>Ilość: <span id="modal-quantity"></span></p>
                <label for="quantityToRemove">Wybierz ilość do usunięcia:</label>
                <input type="number" id="quantityToRemove" name="quantityToRemove" min="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="button" class="btn btn-primary" onclick="deleteItems()">Usuń</button>
            </div>
        </div>
    </div>
</div>
<?php if(!isLoggedIn()) { ?>
  <link rel="stylesheet" href="../assets/css/plugins.min.css"/>
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css"/>
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
<?php if(isLoggedIn()) { ?>
<?php include 'globalnav.php'; ?>
<?php } ?>
</body>
    <script src="../static/jquery.dataTables.min.js"></script>
    <script src="../static/dataTables.bootstrap5.min.js"></script>
<script>
        $(document).ready(function(){
            $('#mytable').DataTable({
                paging: false, // Wyłączenie paginacji
                info: false // Wyłączenie informacji o liczbie rekordów
            });
        });

document.addEventListener("DOMContentLoaded", function() {
    // Pobierz wszystkie przyciski "Usuń"
    var deleteButtons = document.querySelectorAll(".btn-primary");

    // Dodaj obsługę zdarzenia dla każdego przycisku "Usuń"
    deleteButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            // Pobierz dane z wiersza
            var partId = this.closest("tr").getAttribute("data-partid");
            var localization = this.closest("tr").querySelector("td:nth-child(4)").innerText;
            var quantity = parseInt(this.closest("tr").querySelector("td:nth-child(5)").innerText);

            if(localization=="zewnątrz"){
                localization=17;
            }else if(localization=="kooperacja"){
                localization=16;
            }

            // Ustaw wartości w modalu
            document.getElementById("modal-partid").textContent = partId;
            document.getElementById("modal-localization").textContent = localization;
            document.getElementById("modal-quantity").textContent = quantity;

            // Ustaw minimalną i maksymalną ilość w polu wyboru
            document.getElementById("quantityToRemove").min = 0;
            document.getElementById("quantityToRemove").max = quantity;

            // Wyświetl modal
            var myModal = new bootstrap.Modal(document.getElementById('myModal'));
            myModal.show();
        });
    });
});

function deleteItems() {
    // Pobierz wartość z pola wprowadzania quantityToRemove
    var quantityToRemove = document.getElementById("quantityToRemove").value;

    // Pobierz wartości PartID i lokalizacji z modalu
    var partId = document.getElementById("modal-partid").textContent;
    var localization = document.getElementById("modal-localization").textContent;

    // Wyślij zapytanie AJAX do serwera, aby usunąć odpowiednią ilość produktów
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_items.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Obsłuż odpowiedź z serwera (jeśli potrzeba)
            console.log(xhr.responseText);
            location.reload();
        }
    };
    xhr.send("partId=" + partId + "&localization=" + localization + "&quantityToRemove=" + quantityToRemove);

    // Zamknij modal po zakończeniu operacji
    var myModal = new bootstrap.Modal(document.getElementById('myModal'));
    myModal.hide();
}
</script>

</html>