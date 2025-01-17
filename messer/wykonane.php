<!DOCTYPE html>
<html lang="pl">
<?php require_once '../auth.php'; ?>
<?php

function czyCiągZawieraLiczbyPHP($ciąg)
{
    $pattern = '/-?\d+(?:\.\d+)?(?:e-?\d+)?/';
    preg_match($pattern, $ciąg, $matches);

    if (!empty($matches)) {
        return true;
    } else {
        return false;
    }
}

?>


<head>

    <?php include 'globalhead.php'; ?>

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
                        <a class="nav-link" href="main.php">Programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" href="wykonane.php" onclick="localStorage.removeItem('numbermesser')">Zakończone programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="../magazyn/main.php" onclick="localStorage.removeItem('numbermesser')">Magazyn</a>
                      </li>
                    </ul>
                    <?php } ?>
<?php if(isLoggedIn()){ ?>
    <?php if(isSidebar()==0){ ?>
        <div class="container-fluid" style="width:80%;margin-left:16%;">
    <?php }else if(isSidebar()==1){ ?>
        <div class="container-fluid" style="width:90%; margin: 0 auto;">
        <?php } ?>  <?php }else{ ?>

    <div class="container-fluid" style="margin-left:auto;margin-right:auto;">

    <?php } ?>
    <div class="mb-3" style="float:right;">
    <div class="input-group">
    <input type="text"  class="form-control" id="searchInput" placeholder="Nazwa programu...">
</div>
</div>
    <div class="clearfix"></div>

    <div>

        <?php require_once("../dbconnect.php");

        $sql = "SELECT[ProgramName]
        ,[ArchivePacketID]
        ,[SheetName]
        ,[MachineName]
        ,[Material]
        ,[Thickness]
        ,[SheetLength]
        ,[SheetWidth]
        ,[ActualStartTime]
        ,[ActualEndTime]
        ,[ActualState]
        ,[ActualTimeSyncNeeded]
        ,[Comment]
        ,CONVERT (CHAR(8),DATEADD(second, [CuttingTime],0) ,108) as czaspalenia
        FROM [SNDBASE_PROD].[dbo].[Program]
            WHERE 
    [Comment] LIKE '%:%'
ORDER BY
    SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 2, LEN([Comment]) - CHARINDEX(',', [Comment]) - 8) desc;";
        $datas = sqlsrv_query($conn, $sql);

        ?>
        <div class="table-responsive">
            <table class="table table-xl table-hover table-striped" id="mytable"
                   style="font-size: calc(14px + 0.390625vw)">
                <thead>
                <th>Osoba/Powód</th>
                <th>Nazwa Programu</th>
                <th>Nazwa arkusza</th>
                <th>Materiał</th>
                <th>Grubość</th>
                <th>Długość</th>
                <th>szerokość</th>
                <th>czas</th>
                <th>Data i czas</th>
                <?php
                if (isUserMesser()) {
                    ?>
                    <th>Opcje</th>
                <?php }
                ?>
                </thead>
                <tbody class="row_position">
                <?php

                while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) { ?>

                    <?php
                    $wartosci = explode(',', $data['Comment']);
                    $kiersql = "SELECT *
                    FROM PartCheck.dbo.Persons
                    WHERE LOWER([imie_nazwisko]) = LOWER('$wartosci[0]');";
                    $stmt = sqlsrv_query($conn, $kiersql);

                    if (sqlsrv_has_rows($stmt)) {
                        ?>
                    <tr id="main" class="table-success" value="<?php echo $data['ArchivePacketID'] ?>">
                            <td>
                                <?php echo $wartosci[0]; ?>
                            </td>

                            <td>
                                <?php echo "$data[ProgramName]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[SheetName]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[Material]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[Thickness]"; ?>
                            </td>
                            <td>
                                <?php echo ceil($data["SheetLength"]); ?>
                            </td>
                            <td>
                                <?php echo ceil($data["SheetWidth"]); ?>
                            </td>
                            <td>
                                <?php echo "$data[czaspalenia]"; ?>
                            </td>
                            <td><?php echo isset($wartosci[1]) && $wartosci[1] !== '' ? "" . $wartosci[1] : ""; ?></td>
                            <?php
                            if (isUserMesser()) {
                                ?>
                                <td>
                                    <Button class='btn btn-primary btn-sm'>Resetuj</Button>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } else if (
                        stripos($data["Comment"], "nie znaleziono arkusza") !== false ||
                        stripos($data["Comment"], "zla jakosc otworow") !== false ||
                        stripos($data["Comment"], "zla jakosc faz") !== false ||
                        stripos($data["Comment"], "inne") !== false
                    ) { ?>
                        <tr class="table-danger" value="<?php echo $data['ArchivePacketID'] ?>">
                            <td>
                                <?php echo $wartosci[0]; ?>
                            </td>

                            <td>
                                <?php echo "$data[ProgramName]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[SheetName]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[Material]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[Thickness]"; ?>
                            </td>
                            <td>
                                <?php echo ceil($data["SheetLength"]); ?>
                            </td>
                            <td>
                                <?php echo ceil($data["SheetWidth"]); ?>
                            </td>
                            <td>
                                <?php echo "$data[czaspalenia]"; ?>
                            </td>
                            <td>
                                <?php echo isset($wartosci[1]) && $wartosci[1] !== '' ? "" . $wartosci[1] : ""; ?>
                            </td>
                            <?php if (isUserMesser()) {
                                ?>
                                <td>
                                    <Button class='btn btn-primary btn-sm'>Resetuj</Button>
                                </td>

                            <?php } ?>
                        </tr>
                    <?php } ?>

                <?php }
                ?>


                </tbody>
            </table>
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
<script>
    $(document).ready(function () {
        $('.btn-primary').on('click', function () {
            var button = $(this);
            var rowId = button.closest('tr').attr('value'); // Pobierz ID z atrybutu 'value' rodzica przycisku

            // Wywołaj AJAX, aby zaktualizować dane w bazie danych
            $.ajax({
                url: 'resetuj.php', // Ścieżka do pliku PHP, który obsłuży aktualizację bazy danych
                method: 'POST',
                data: {rowId: rowId}, // Przesyłanie ID wiersza do serwera
                success: function (response) {
                    console.log(response); // Wyświetl odpowiedź z serwera w konsoli przeglądarki
                    button.text('Zresetowano'); // Zmień tekst przycisku na "Zakończono"
                    button.removeClass('btn-primary').addClass('btn-success'); // Zmień styl przycisku na zielony
                    button.prop('disabled', true); // Zablokuj przycisk, aby uniknąć kolejnych kliknięć
                }
            });
        });
    });

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
</script>

</html>