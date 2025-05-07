<?php require_once '../auth.php'; ?>
<?php

try {
    $dsn = "pgsql:host=10.100.100.42;port=5432;dbname=hrappka;";
    $username = "hrappka";
    $password = "1UjJ7DIHXO3YpePh";

    // Utworzenie instancji PDO
    $pdo = new PDO($dsn, $username, $password);

    // Ustawienie opcji PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}
if (isset($_GET['project_name'])) {
    $rfid = $_GET['project_name'];
}
$sql = "select
		ut_id as cuce_task,
		cr_id as cuce_request,
        ut_name AS cuce_category_detail_additional,
        cr_contractor_fkey,
        COALESCE(cps_id, 1110) AS cuce_position
    from company_contractor_requests AS request_event
    LEFT JOIN user_tasks ON request_event.cr_id = ut_entity_fkey and ut_deadline_time > NOW()
    left join company_contractor_positions on cps_contractor_fkey=cr_contractor_fkey  and cps_deleted = false and cps_archival = false
    WHERE ut_deleted IS false
      and request_event.cr_number = '$rfid'
      and ut_entity_type = 'contractors-requests';";
$stmt = $pdo->query($sql);

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
            left: 84.5%;
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
            <div class="row">
                <h1><center><b><?php echo  $rfid; ?></b></center></h1>
                <br /><br />
            <?php
            while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Tutaj możesz wykonywać operacje na każdym wierszu
            ?>
                <div class="col-xl-4 col-lg-4">
                    <a href="registerwork.php?cuce_task=<?php echo $row1['cuce_task']; ?>&cuce_request=<?php echo $row1['cuce_request']; ?>&cuce_category_detail_additional=<?php echo $row1['cuce_category_detail_additional']; ?>&cr_contractor_fkey=<?php echo $row1['cr_contractor_fkey']; ?>&cuce_position=<?php echo $row1['cuce_position']; ?>&project=<?php echo  $rfid; ?>">
                        <div class="card l-bg-cherry">
                            <div class="card-statistic-3 p-4">
                                <div class="mb-4">
                                    <h5 class="card-title mb-0"><center><?php echo $row1['cuce_category_detail_additional']; ?></center></h5>
                                </div>
                                </div>
                                
                                </div>
                        </a>
                    </div>
<?php } ?>
            </div>
            <?php if (!isUserParts()) { ?>
                        <?php if (!isUserPartsKier()) { ?>
                            <div class="btn-toolbar position-fixed" role="toolbar" aria-label="Toolbar with button groups" style="bottom:4%;">
                <div class="btn-group me-2 " role="group" aria-label="First group"></div>
                            <button type="button" onclick="window.history.go(-1);" class="btn btn-warning btn-lg">Powrót
                            </button>
                            </div></div>
                        <?php } ?>
                        <?php } ?>
</body>
<?php if (!isUserPartskier() and !isLoggedIn()) { ?>
    <script>
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