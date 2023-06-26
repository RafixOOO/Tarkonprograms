<?php
require 'vendor\autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parts</title>
    <?php require_once('globalhead.php') ?>
</head>
<body class = "bg-secondary p-2 text-dark bg-opacity-25">
    <br />
    <div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <label class="form-label" for="customFile">Przesyłanie pliku Excel</label>
        <input type="file" class="form-control" id="customFile"  name="excelFile" accept=".xlsx, .xls"/>
        <p class="text-muted"><h6>Akceptowalne roszerzenie .xls i .xlsx</h6></p>
        <button type="submit" name="submit" class="btn btn-outline-warning text-dark">Wyślij</button>
        <a class="btn btn-outline-warning text-dark" href="index.php" role="button">Wróć</a>
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
                        $sql = "SELECT * FROM Parts WHERE Zespol = '{$row['B']}' AND Pozycja = '{$row['C']}'";
                        $result = sqlsrv_query($conn, $sql);
                        if (sqlsrv_has_rows($result)) {
                            $sql = "UPDATE Parts SET [Zespol] = '{$row['B']}', [Pozycja] = '{$row['C']}', [Ilosc] = {$row['D']}, [Profil] = '{$row['E']}', [Material] = '{$row['F']}', [Dlugosc] = '{$row['G']}', [Ciezar] = '{$row['H']}', [Calk_ciez] = '{$row['I']}', [Uwaga] = '{$row['J']}',
                            [Projekt] = '{$row['A']}'  WHERE Zespol = '{$row['B']}' AND Pozycja = '{$row['C']}'";
                            sqlsrv_query($conn, $sql);
                            continue;
                        }

                        $sql = "INSERT INTO Parts ([Projekt], [Zespol], [Pozycja], [Ilosc], [Profil], [Material], [Dlugosc], [Ciezar], [Calk_ciez], [Uwaga]) VALUES ('{$row['A']}', '{$row['B']}', {$row['C']}, '{$row['D']}', '{$row['E']}', '{$row['F']}', '{$row['G']}', '{$row['H']}', '{$row['I']}', '{$row['J']}')";
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
