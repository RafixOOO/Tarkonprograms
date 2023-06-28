<!DOCTYPE html>
<html>
<?php
require_once("dbconnect.php");
$sqlprojekt = "SELECT DISTINCT p.Projekt FROM dbo.Parts p, dbo.PartArchive_Messer m, dbo.Product_V630 b WHERE p.Pozycja != m.PartName COLLATE Latin1_General_CS_AS AND b.[Name] != p.[Pozycja]";
$datadodaj = sqlsrv_query($conn, $sqlprojekt);
?>

<head>
    <title>Parts</title>
    <?php require_once('globalhead.php') ?>
</head>

<body class="bg-secondary p-2 text-dark bg-opacity-25">
    <div class="container">
        <form method="POST" action=''>
            <br />
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Projekt:</label>
                <div class="col-sm-6">
                    <select class="form-control" name="projekt" id="projekt">
                        <option value="" disabled selected>Projekt</option>
                        <?php while ($datadod = sqlsrv_fetch_array($datadodaj, SQLSRV_FETCH_ASSOC)) {
                            echo "<option value='" . $datadod['Projekt'] . "'>" . $datadod['Projekt'] . "</option>";
                        } sqlsrv_close($conn); ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Zespół:</label>
                <div class="col-sm-6">
                    <select class="form-control" name="zespol" id="zespol">
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Detal:</label>
                <div class="col-sm-6">
                    <select class="form-control" name="detal" id="detal">
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Ilość:</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control" placeholder="Ilość" name="ilosc">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Długość:</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control" placeholder="Długość" name="dlugosc">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Wykonał:</label>
                <div class="col-sm-6">
                    <select class="form-control" name="comment">
                        <option value="" disabled selected>Wykonał</option>
                        <option value="SYLWESTER WOZNIAK">SYLWESTER WOZNIAK</option>
                        <option value="MARCIN MICHAS">MARCIN MICHAS</option>
                        <option value="LUKASZ PASEK">LUKASZ PASEK</option>
                        <option value="ARTUR BEDNARZ">ARTUR BEDNARZ</option>
                        <option value="DARIUSZ MALEK">DARIUSZ MALEK</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button class="btn btn-outline-warning" type="submit">Zapisz</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-warning" href="index.php" role="button">Anuluj</a>
                </div>
            </div>
        </form>
    </div>
    
    <script>
    $(document).ready(function() {
        $("#projekt").change(function() {
            updateZespoly();
        });

        $("#zespol").change(function() {
            updateDetale();
        });

        function updateZespoly() {
            var projekt1 = $("#projekt").val();
            $.ajax({
                url: "pobierz_zespoly.php",
                method: "POST",
                data: { projekt1: projekt1 },
                success: function(response) {
                    var zespoly = response.split(',');

                    $("#zespol").empty();

                    for (var i = 0; i < zespoly.length; i++) {
                        $("#zespol").append("<option value='" + zespoly[i] + "'>" + zespoly[i] + "</option>");
                    }
                    updateDetale();
                },
                error: function(xhr, status, error) {
                    console.log("Błąd zapytania Ajax: " + error);
                }
            });
        }

        function updateDetale() {
            var projekt1 = $("#projekt").val();
            var zespol = $("#zespol").val();

            $.ajax({
                url: "pobierz_detale.php",
                method: "POST",
                data: {
                    projekt1: projekt1,
                    zespol: zespol
                },
                success: function(response) {
                    var detale = response.split(',');

                    $("#detal").empty();

                    for (var i = 0; i < detale.length; i++) {
                        $("#detal").append("<option value='" + detale[i] + "'>" + detale[i] + "</option>");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Błąd zapytania Ajax: " + error);
                }
            });
        }
    });
    </script>
</body>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $projekt = $_POST["projekt"];
    $zespol = $_POST["zespol"];
    $detal = $_POST["detal"];
    $ilosc = $_POST["ilosc"];
    $dlugosc = $_POST["dlugosc"];
    $wykonawca = $_POST["comment"];

    $sql = "INSERT INTO Product_Recznie (Projekt, Zespol, Pozycja, Ilosc_zrealizowana, Długosc_zrealizowana, Osoba) VALUES (?, ?, ?, ?, ?, ?)";
    $params = array($projekt, $zespol, $detal, $ilosc, $dlugosc, $wykonawca);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Dane zostały zapisane.";
    }
}



?>

</html>
