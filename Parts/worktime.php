<?php require_once '../auth.php'; ?>

<?php
$identyfikator = isset($_GET['identyfikator']) && $_GET['identyfikator'] !== '' ? $_GET['identyfikator'] : null;
require_once("../dbconnect.php");
$params = array($identyfikator);
$sql1 = "SELECT PersonsID FROM dbo.PersonsID WHERE identyfikator = ?";
$stmt2 = sqlsrv_query($conn, $sql1, $params);

if ($stmt2 === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);

if ($row2) {
    $personID = $row2['PersonsID'];
} else {
    echo "Nie znaleziono rekordu dla podanego identyfikatora.";
}

$sql = "SELECT * 
        FROM HrapWorkTime 
        WHERE (cuce_date_to IS NULL OR cuce_date_to = CAST(GETDATE() AS DATE)) and PersonID=$personID
        ORDER BY cuce_date_to asc";

$datas = sqlsrv_query($conn, $sql);

if ($datas === false) {
    die(print_r(sqlsrv_errors(), true)); // Wyświetlenie błędu, jeśli zapytanie się nie powiedzie
}

?>
<!DOCTYPE html>
<html>

<head lang="PL">
    <?php
    require_once('globalhead.php');
    ?>
    <meta charset="utf-8" />
    <style>
        .verticalrotate {
            position: fixed;
            bottom: 50%;
            right: 84.5%;
            width: 30%;
            transform: rotate(-90deg);
        }
    </style>
    <?php if(!isLoggedIn()){ ?>
    <script>
        // Sprawdzenie localStorage przed załadowaniem strony
        const number1 = localStorage.getItem('number1');
        if (!number1) {
            // Jeśli dane są puste, przekierowanie na inną stronę
            window.location.href = 'panel.php'; // Podaj adres strony błędu lub logowania
        }
    </script>
    <?php } ?>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
<div class="progress verticalrotate">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
            </div>
            <br/><br/>
            <div class="container-fluid" style="margin-left:auto;margin-right:auto;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="4">Imię i nazwisko:</br><center><span id="nazwa"></span></center> </th>
                            <th colspan="2" style="width: 30%;">RFID:</br><center><span id="rfid"></span></center> </th>
                        </tr>
                        <tr>
                            <th><center>Projekt</center></th>
                            <th><center>Czynność</center></th>
                            <th><center>Data rozpoczęcia</center></th>
                            <th><center>Data zakończenia</center></th>
                            <th><center>Czas rozpoczęcia</center></th>
                            <th><center>Czas zakończenia</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php 
                        $totalHours = 0;
                        $totalMinutes = 0;
                        $userID=0;
                        $project="";
                        while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) { 
                            $userID = $row['PersonID'];
                            $project = $row['Project'];
                            
                            ?>
                            
                        <tr>
                            <th><center><?php echo $row['Project']; ?></center></th>
                            <th><center><?php echo $row['cuce_category_detail_additional']; ?></center></th>
                            <th><center><?php echo $row['cuce_date'] ? $row['cuce_date']->format('Y-m-d') : '&mdash;'; ?></center></th>
                            <th><center><?php echo $row['cuce_date_to'] ? $row['cuce_date_to']->format('Y-m-d') : '&mdash;'; ?></center></th>
                            <th><center><?php echo $row['cuce_time_from'] ? $row['cuce_time_from'] : '&mdash;'; ?></center></th>
                            <th><center><?php echo $row['cuce_time_to'] ? $row['cuce_time_to'] : '&mdash;'; ?></center></th>
                        </tr>
                        
                        <?php 
                     if (
                        !empty($row['cuce_date']) &&
                        !empty($row['cuce_date_to']) &&
                        !empty($row['cuce_time_from']) &&
                        !empty($row['cuce_time_to'])
                    ) {
                        // Jeśli daty są typu DATE jako string
                        $dateFrom = $row['cuce_date']->format('Y-m-d') . ' ' . $row['cuce_time_from'];
                        $dateTo = $row['cuce_date_to']->format('Y-m-d') . ' ' . $row['cuce_time_to'];
                    
                        try {
                            $startDateTime = new DateTime($dateFrom);
                            $endDateTime = new DateTime($dateTo);
                    
                            if ($endDateTime > $startDateTime) {
                                $interval = $startDateTime->diff($endDateTime);
                    
                                $diffHours = $interval->days * 24 + $interval->h;
                                $diffMinutes = $interval->i;
                    
                                $totalHours += $diffHours;
                                $totalMinutes += $diffMinutes;
                            }
                        } catch (Exception $e) {
                            // obsługa błędu, np. pomiń albo zaloguj
                        }
                    }
                }
                    // Korekta minut na godziny
                    $totalHours += floor($totalMinutes / 60);
                    $totalMinutes = $totalMinutes % 60;
                    
                    // Formatowanie końcowego czasu
                    $totalFormatted = sprintf('%02d:%02d', $totalHours, $totalMinutes);
                     ?>
                    </tbody>
                    <tfoot>
    <tr>
        <th colspan="4" style="text-align: right;">Łączny czas:</th>
        <th colspan="2"><center><?php echo $totalFormatted; ?></center></th>
    </tr>
</tfoot>
                </table>
                <div class="btn-toolbar position-fixed" role="toolbar" aria-label="Toolbar with button groups" style="bottom:4%;">
                <div class="btn-group me-2 " role="group" aria-label="First group">
                <button type="button" onclick="localStorage.removeItem('number1'); window.location.href = 'panel.php';" class="btn btn-warning btn-lg"><img src="../static/box-arrow-right.svg" alt="Wyjdź" style="width:20px; height:20px;">
                </button>
                    <?php if (isset($_GET['finish']) && $_GET['finish']==1) { ?>
                        <script>
                            userID="<?php echo $userID; ?>";
                            </script>
                <button type="button" onclick=" window.location.href = 'finishwork.php?rfid='+userID;" class="btn btn-info btn-lg"><img src="../static/power.svg" alt="Finish" style="width:20px; height:20px;">
                </button>
                <?php }else if(isset($_GET['finish']) && $_GET['finish']==2){ ?>
                    <script>
                    project="<?php echo $project; ?>";
                    </script>
                    <button type="button" onclick="window.location.href = 'receiver.php?project_name=' + project;" class="btn btn-success btn-lg"><img src="../static/list-columns-reverse.svg" alt="Detale" style="width:20px; height:20px;">
                </button>
                <button type="button" onclick="window.location.href = 'main.php'" class="btn btn-info btn-lg"><img src="../static/play-fill.svg" alt="Start" style="width:20px; height:20px;">
                </button>
                    <?php } ?>
                            </div></div>

            </div>

</body>
<?php if (!isUserPartskier() and !isLoggedIn()) { ?>
    <script>
         document.getElementById('nazwa').textContent = localStorage.getItem('nazwa');
         document.getElementById('rfid').textContent = localStorage.getItem('number1');
        var stored = localStorage.getItem('number1');
        if (stored !== null) {
            var colorButton = document.getElementById('time');
            var percent = 0;

            function changeColor() {
                percent += 0.1;
                colorButton.style.width = `${percent}%`;

                if (percent < 100) {
                    setTimeout(changeColor, 200); // Powtórz co 1 sekundę (1000 milisekund)
                    localStorage.setItem('czas', percent);
                } else {
                    localStorage.removeItem('number1');
                    localStorage.removeItem('czas');
                    window.location.href = 'panel.php';
                }
            }

            changeColor(); // Wywołaj funkcję changeColor() po załadowaniu strony
        }

        setTimeout(changeColor, 5000);

        setTimeout(changeColor, 1000); // Rozpocznij po 5 sekundach

        function sendcheck() {
            usernumber = document.getElementById('user-number');
            sendForm(userNumber);
        }
    </script>
<?php } ?>
</html>