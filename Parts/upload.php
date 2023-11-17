<?php
require 'vendor\autoload.php';

require_once('../auth.php');

if(!isLoggedIn()){
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parts</title>
    <?php require_once('globalhead.php') ?>
</head>
<body class = "p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<input type="hidden" id="darkModeButton" />
    <br />
    <div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <label class="form-label" for="customFile">Przesyłanie pliku CSV</label>
        <input type="file" class="form-control" id="customFile" name="csvFile" accept=".csv"/>
        <p class="text-muted"><h6>Akceptowalne rozszerzenie .csv</h6></p>
        <button type="submit" name="submit" class="btn btn-outline-warning text-dark">Wyślij</button>
        <a class="btn btn-outline-warning text-dark" href="main.php" role="button">Wróć</a>
    </form>
    </div>
    <?php
    if (isset($_POST['submit'])) {
        require_once('dbconnect.php');
        
        if (isset($_FILES['csvFile'])) {
            $file = $_FILES['csvFile'];

            $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($fileType != 'csv') {
                die("Niewłaściwy format pliku. Akceptowane rozszerzenie to .csv.");
            }

            $targetDirectory = "Files/";
            $targetFile = $targetDirectory . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo "Plik został przesłany i zapisany na serwerze. Numer Importu: ";

                

                    $sqlimport="Select Max(Id_import) as import from dbo.Parts";
                        $resultimport = sqlsrv_query($conn, $sqlimport);
                    while ($row = sqlsrv_fetch_array($resultimport, SQLSRV_FETCH_ASSOC)) {
                        $id_import=$row['import'];
                    }
                    $id_import++;
                    $isFirstRow = 0;
                    echo $id_import;
                    if (($handle = fopen($targetFile, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                            if (!empty($data[0])) {

                        if ($isFirstRow==2) {
                            $isFirstRow++;
                            continue;
                        }

                        if (strpos($data[2], 'rev') !== false) {
                            $dlugoscCiągu = strlen($data['2']);
                            $tekstBezOstatnichCyfr = substr($data['2'], 0, $dlugoscCiągu - 5);
                            $sql = "SELECT * FROM Parts WHERE Zespol = '{$data['1']}' AND Pozycja = '{$data['2']}'";
                            $result = sqlsrv_query($conn, $sql);
                            if (!sqlsrv_has_rows($result)) {
                                $tekst=$tekstBezOstatnichCyfr.'%';
                                $sql1 = "UPDATE [PartCheck].[dbo].[Parts] SET
                                    [lock] = 1
                                    WHERE Zespol = '{$data['1']}' and Pozycja LIKE '{$tekst}'";
                                    
                                sqlsrv_query($conn, $sql1);

                                $sqlinsert = "INSERT INTO [PartCheck].[dbo].[Product_Recznie] (Projekt, Pozycja)
                                SELECT Projekt, Pozycja
                                FROM [PartCheck].[dbo].[Parts]
                                where Zespol = '{$data['1']}' and Pozycja LIKE '{$tekst}'";

                                sqlsrv_query($conn, $sqlinsert);
                                
                            }
                        }
                        
                        $sql = "SELECT * FROM Parts WHERE Zespol = '{$data['1']}' AND Pozycja = '{$data['2']}'";
                        $result = sqlsrv_query($conn, $sql);
                        
                        if (sqlsrv_has_rows($result)) {
                            $sql = "UPDATE Parts SET
                                [Zespol] = '{$data['1']}',
                                [Pozycja] = '{$data['2']}',
                                [Ilosc] = '{$data['3']}',
                                [Profil] = '{$data['4']}',
                                [Material] = '{$data['5']}',
                                [Dlugosc] = '{$data['6']}',
                                [Ciezar] = '{$data['7']}',
                                [Calk_ciez] = '{$data['8']}',
                                [Uwaga] = '{$data['9']}',
                                [Projekt] = '{$data['0']}'
                                WHERE Zespol = '{$data['1']}' AND Pozycja = '{$data['2']}'";
                                
                            sqlsrv_query($conn, $sql);
                            continue;
                        }
                
                        $sql = "INSERT INTO Parts ([Projekt], [Zespol], [Pozycja], [Ilosc], [Profil], [Material], [Dlugosc], [Ciezar], [Calk_ciez], [Uwaga], [Id_import])
                                VALUES ('{$data['0']}', '{$data['1']}', '{$data['2']}', '{$data['3']}', '{$data['4']}', '{$data['5']}', '{$data['6']}', '{$data['7']}', '{$data['8']}', '{$data['9']}', '{$id_import}')";
                        // Użyj prepared statement, aby uniknąć problemów z SQL Injection
                        sqlsrv_query($conn, $sql);
                    }
                    }

                        if (sqlsrv_query($conn, $sql) === False) {
                            echo "Błąd podczas zapisywania danych do bazy danych: " . sqlsrv_errors();
                        }
                    }
            } else {
                echo "Wystąpił błąd podczas przesyłania pliku na serwer.";
            }
        }
        if($_SESSION['imie_nazwisko']==""){
            logUserActivity($wykonawca,'Zaktualizował aplikację parts dodając nowy plik do wczytania '. $id_import);
          }else{
            logUserActivity($_SESSION['imie_nazwisko'],'Zaktualizował aplikację parts dodając nowy plik do wczytania '. $id_import);
          }
          fclose($handle);
        sqlsrv_close($conn);
    }
    ?>
</body>
</html>
