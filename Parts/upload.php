<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parts</title>
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
