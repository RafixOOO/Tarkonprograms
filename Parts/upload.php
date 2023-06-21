<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Przesyłanie pliku Excel</title>
    <meta charset ="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
</head>
<body>
    <br />
    <div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <label class="form-label" for="customFile">Przesyłanie pliku Excel</label>
        <input type="file" class="form-control" id="customFile"  name="excelFile" accept=".xlsx, .xls"/>
        <p class="text-muted"><h6>Akceptowalne roszerzenie .xls i .xlsx</h6></p>
        <button type="submit" name="submit" class="btn btn-outline-primary">Wyślij</button>
    </form>
    </div>
    <?php
    if (isset($_POST['submit'])) {
        require_once('dbconnect.php');

        if (isset($_FILES['excelFile'])) {
            $file = $_FILES['excelFile'];

            $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($fileType != 'xlsx' && $fileType != 'xls') {
                die("Niewłaściwy format pliku. Akceptowane rozszerzenia to .xlsx i .xls.");
            }

            $targetDirectory = "Files/";
            $targetFile = $targetDirectory . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo "Plik został przesłany i zapisany na serwerze.";

                try {
                    $spreadsheet = IOFactory::load($targetFile);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray(null, true, true, true);
                    $isFirstRow = true;
                    foreach ($rows as $row) {
                        if ($isFirstRow) {
                            $isFirstRow = false;
                            continue;
                        }
                        $sql = "SELECT * FROM Parts WHERE Zespol = '$row[A]' AND Pozycja = '$row[B]'";
                        $result = sqlsrv_query($conn, $sql);
                        
                        if (sqlsrv_num_rows($result)>0) {
                            $sql = "UPDATE `parts` SET `Zespol`='$row[A]',`Pozycja`='$row[B]',`Ilosc`='$row[C]',`Profil`='$row[D]',`Material`='$row[E]',`Dlugosc`='$row[F]',`Ciezar`='$row[G]',`Calk_Ciezar`='$row[H]',`Uwaga`='$row[I]' WHERE Zespol = '$row[A]' AND Pozycja = '$row[B]'";
                            sqlsrv_query($conn, $sql);
                            continue;
                        }

                        $sql = "INSERT INTO `parts`(`Zespol`, `Pozycja`, `Ilosc`, `Profil`, `Material`, `Dlugosc`, `Ciezar`, `Calk_Ciezar`, `Uwaga`) VALUES ('$row[A]','$row[B]','$row[C]','$row[D]','$row[E]','$row[F]','$row[G]','$row[H]','$row[I]')";

                        if (sqlsrv_query($conn, $sql) === False) {
                            echo "Błąd podczas zapisywania danych do bazy danych: " . sqlsrv_errors();
                        }
                    }
                } catch (Exception $e) {
                    die('Wystąpił błąd podczas wczytywania pliku Excel: ' . $e->getMessage());
                }
            } else {
                echo "Wystąpił błąd podczas przesyłania pliku na serwer.";
            }
        }

        sqlsrv_close($conn);
    }
    ?>
</body>
</html>
