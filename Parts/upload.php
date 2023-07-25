<?php
require 'vendor\autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once('../auth.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parts</title>
    <?php require_once('globalhead.php') ?>
</head>
<body class = "p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <br />
    <div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <label class="form-label" for="customFile">Przesyłanie pliku Excel</label>
        <input type="file" class="form-control" id="customFile"  name="excelFile" accept=".xlsx, .xls"/>
        <p class="text-muted"><h6>Akceptowalne roszerzenie .xls i .xlsx</h6></p>
        <button type="submit" name="submit" class="btn btn-outline-warning text-dark">Wyślij</button>
        <a class="btn btn-outline-warning text-dark" href="main.php" role="button">Wróć</a>
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

                    $sqlimport="Select Max(Id_import) as import from dbo.Parts";
                        $resultimport = sqlsrv_query($conn, $sqlimport);
                    while ($row = sqlsrv_fetch_array($resultimport, SQLSRV_FETCH_ASSOC)) {
                        $id_import=$row['import'];
                    }
                    $id_import++;
                    
                    foreach ($rows as $row) {
                        if ($isFirstRow) {
                            $isFirstRow = false;
                            continue;
                        }

                        if (strpos($row['C'], 'rev') !== false) {
                            $dlugoscCiągu = strlen($row['C']);
                            $tekstBezOstatnichCyfr = substr($row['C'], 0, $dlugoscCiągu - 5);
                            $sql = "SELECT * FROM Parts WHERE Zespol = '{$row['B']}' AND Pozycja = '{$row['C']}'";
                            $result = sqlsrv_query($conn, $sql);
                            if (!sqlsrv_has_rows($result)) {
                                $tekst=$tekstBezOstatnichCyfr.'%';
                                $sql1 = "UPDATE [PartCheck].[dbo].[Parts] SET
                                    [lock] = 1
                                    WHERE Zespol = '{$row['B']}' and Pozycja LIKE '{$tekst}'";
                                    
                                sqlsrv_query($conn, $sql1);

                                $sqlinsert = "INSERT INTO [PartCheck].[dbo].[Product_Recznie] (Projekt, Pozycja)
                                SELECT Projekt, Pozycja
                                FROM [PartCheck].[dbo].[Parts]
                                where Zespol = '{$row['B']}' and Pozycja LIKE '{$tekst}'";

                                sqlsrv_query($conn, $sqlinsert);
                                
                            }
                        }
                        
                        $sql = "SELECT * FROM Parts WHERE Zespol = '{$row['B']}' AND Pozycja = '{$row['C']}'";
                        $result = sqlsrv_query($conn, $sql);
                        
                        if (sqlsrv_has_rows($result)) {
                            $sql = "UPDATE Parts SET
                                [Zespol] = '{$row['B']}',
                                [Pozycja] = '{$row['C']}',
                                [Ilosc] = '{$row['D']}',
                                [Profil] = '{$row['E']}',
                                [Material] = '{$row['F']}',
                                [Dlugosc] = '{$row['G']}',
                                [Ciezar] = '{$row['H']}',
                                [Calk_ciez] = '{$row['I']}',
                                [Uwaga] = '{$row['J']}',
                                [Projekt] = '{$row['A']}'
                                WHERE Zespol = '{$row['B']}' AND Pozycja = '{$row['C']}'";
                                
                            sqlsrv_query($conn, $sql);
                            continue;
                        }
                
                        $sql = "INSERT INTO Parts ([Projekt], [Zespol], [Pozycja], [Ilosc], [Profil], [Material], [Dlugosc], [Ciezar], [Calk_ciez], [Uwaga], [Id_import])
                                VALUES ('{$row['A']}', '{$row['B']}', '{$row['C']}', '{$row['D']}', '{$row['E']}', '{$row['F']}', '{$row['G']}', '{$row['H']}', '{$row['I']}', '{$row['J']}', '{$id_import}')";

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
        if($_SESSION['imie_nazwisko']==""){
            logUserActivity($wykonawca,'Zaktualizował aplikację parts dodając nowy plik do wczytania');
          }else{
            logUserActivity($_SESSION['imie_nazwisko'],'Zaktualizował aplikację parts dodając nowy plik do wczytania');
          }
        
        sqlsrv_close($conn);
    }
    ?>
</body>
</html>
