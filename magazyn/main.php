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
<?php if(isLoggedIn()){ ?>
<?php require_once("navbar.php"); ?>
<br /><br /><br /><br />
<?php } ?>
    <!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<?php if(!isLoggedIn()){ ?>
<ul class="nav nav-pills nav-primary" style="margin-left:auto;margin-right:auto;">
                      <li class="nav-item">
                        <a class="nav-link" href="../messer/main.php">Programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="../messer/wykonane.php" onclick="localStorage.removeItem('numbermesser')">Zakończone programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" href="main.php" onclick="localStorage.removeItem('numbermesser')">Magazyn</a>
                      </li>
                    </ul>
                    <?php } ?>
<?php if(isLoggedIn()){ ?>
    <?php if(isSidebar()==0){ ?>
        <div class="container-fluid" style="width:80%;margin-left:16%;">
    <?php }else if(isSidebar()==1){ ?>
        <div class="container-fluid" style="width:90%; margin: 0 auto;">
        <?php } ?>
  <?php }else{ ?>

    <div class="container-fluid" style="margin-left:auto;margin-right:auto;">

    <?php } ?>
<div class="table-responsive">
    <?php if (isUserMesser()) { ?>
<div><Button class='btn btn-success' data-bs-toggle="modal" data-bs-target="#ModalAdd">Dodaj</Button></div>
<?php } ?>
<?php
echo "<table class='table table-xl table-hover table-striped' id='mytable'
                   style='font-size: calc(14px + 0.390625vw)'>";
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
        <th>szerokość</th>
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
    echo "<td>" . round($row['Thickness'], 2) . "</td>";
echo "<td>" . round($row['Length'], 2) . "</td>";
echo "<td>" . round($row['Width'], 2) . "</td>";
     if (isUserMesser()) {
        echo "<td> <Button class='btn btn-primary'>Transport / Usuń</Button></td>";
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
                <h5 class="modal-title" id="exampleModalLabel">Aktualizowanie produktu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>PartID: <span id="modal-partid"></span></p>
                <p>Lokalizacja: <span id="modal-localization"></span></p>
                <p>Ilość: <span id="modal-quantity"></span></p>
                <p><label for="quantityToRemove">Wybierz ilość:</label>
                <input type="number" id="quantityToRemove" name="quantityToRemove" min="0" style="width: 100px;"></p>
                <p><input type="checkbox" id="transport" name="transport">
                <label for="transport">Transport?</label></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Anuluj</button>
                <button type="button" class="btn btn-primary" onclick="deleteItems()">Aktualizuj</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ModalAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Dodawanie produktu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p>Arkusz: <input type="text" id="modal-partid1" name="modal-partid"></p>
<p>Lokalizacja: 
    <select id="modal-localization1" name="modal-localization">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="17">Zewnątrz</option>
        <option value="16">Kooperacja</option>
    </select>
</p>
<p>Ilość: 
    <input type="number" id="modal-quantity1" name="modal-quantity" min="0">
</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Anuluj</button>
                <button type="button" class="btn btn-primary" onclick="addItems()">dodaj</button>
            </div>
        </div>
    </div>
</div>
<?php if(!isLoggedIn()) { ?>
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
         $(document).ready(function() {
    $('#mytable').DataTable({
        order: [[1, 'asc']], // Domyślne sortowanie
        ordering: false,
        paging: false     // Wyłączenie sortowania przez użytkownika
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

    // Pobierz stan checkboxa (czy jest zaznaczony)
    var isTransportChecked = document.getElementById("transport").checked;

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

    // Przygotuj dane do wysłania, uwzględniając stan checkboxa
    var data = "partId=" + partId + "&localization=" + localization + "&quantityToRemove=" + quantityToRemove + "&transport=" + (isTransportChecked ? "1" : "0");

    // Wysyłanie danych
    xhr.send(data);

    // Zamknij modal po zakończeniu operacji
    var myModal = new bootstrap.Modal(document.getElementById('myModal'));
    myModal.hide();
}

function addItems() {
    // Pobierz wartości z modalu
    var partId = document.getElementById("modal-partid1").value;
    var localization = document.getElementById("modal-localization1").value;
    var quantity = document.getElementById("modal-quantity1").value;

    // Debugowanie: logowanie pobranych wartości
    console.log("partId: " + partId);
    console.log("localization: " + localization);
    console.log("quantity: " + quantity);

    // Sprawdź, czy dane są poprawne
    if (!partId || !localization || !quantity || isNaN(quantity) || quantity <= 0) {
        alert("Proszę wypełnić wszystkie pola poprawnie.");
        return;
    }

    // Wyślij zapytanie AJAX do serwera
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_items.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText);
            location.reload(); // Odświeżenie strony po zakończeniu operacji
        }
    };

    // Przesyłanie danych do serwera
    xhr.send("partId=" + encodeURIComponent(partId) + 
             "&localization=" + encodeURIComponent(localization) + 
             "&quantityToRemove=" + encodeURIComponent(quantity));

    // Zamknij modal po zakończeniu operacji
    var myModal = new bootstrap.Modal(document.getElementById('ModalAdd'));
    myModal.hide();
}
</script>

</html>