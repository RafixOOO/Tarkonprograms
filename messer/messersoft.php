<!DOCTYPE html>

<?php
require_once '../auth.php';
?>
<html>

<head>
    <?php include 'globalhead.php'; ?>
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            /* Distributes space between columns */
        }

        .columnleft,
        .columnright {
            width: 45%;
            /* Adjust as needed */
            box-sizing: border-box;
            /* Ensures padding and border are included in width */
        }

        .columnleft {
            text-align: left;
        }

        .columnright {
            text-align: left;
        }

        .search-container {
            margin-bottom: 10px;
        }

        .search-container input {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
    </style>
    <script>
        function submitForm() {
            document.getElementById("dateForm").submit();
        }
    </script>
</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <div class="container-fluid" style="width:80%;margin-left:auto;margin-right:auto;">

        <?php
        $formattedDate = date('Y-m-d');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Pobranie wybranej daty z formularza
            $selectedDate = $_POST['selected_date'];

            // Walidacja i przetwarzanie daty
            if (!empty($selectedDate)) {
                // Formatowanie wybranej daty
                $formattedDate = date('Y-m-d', strtotime($selectedDate));
            } else {
                // Ustawienie dzisiejszej daty, jeśli brak wybranej daty
                $formattedDate = date('Y-m-d');
            }
        }
        require_once("dbconnect.php");
        $sql = "SELECT 
j2.id,
    LEFT(
        SUBSTRING(
            JSON_VALUE(j2.msg, '$.PartProgramName'), 
            LEN(JSON_VALUE(j2.msg, '$.PartProgramName')) - CHARINDEX('/', REVERSE(JSON_VALUE(j2.msg, '$.PartProgramName'))) + 2, 
            LEN(JSON_VALUE(j2.msg, '$.PartProgramName'))
        ), 
        LEN(SUBSTRING(
            JSON_VALUE(j2.msg, '$.PartProgramName'), 
            LEN(JSON_VALUE(j2.msg, '$.PartProgramName')) - CHARINDEX('/', REVERSE(JSON_VALUE(j2.msg, '$.PartProgramName'))) + 2, 
            LEN(JSON_VALUE(j2.msg, '$.PartProgramName'))
        )) - 4
    ) AS PartProgramName,
    DATEADD(hour, 2, j2.[_internal_timestamp]) AS Starttime,
        DATEADD(hour, 2, j2.[_internal_endtime]) AS Endtime,
    -- Czas trwania dla każdego typu stanu
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, SUM(CASE WHEN j.StatusType = 'CUTTING' THEN CAST(j.Duration AS float) ELSE 0 END), 0), 108), 
        '00:00:00'
    ) AS CuttingDuration,
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, SUM(CASE WHEN j.StatusType = 'ERROR' THEN CAST(j.Duration AS float) ELSE 0 END), 0), 108), 
        '00:00:00'
    ) AS ErrorDuration,
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, SUM(CASE WHEN j.StatusType = 'IDLE' THEN CAST(j.Duration AS float) ELSE 0 END), 0), 108), 
        '00:00:00'
    ) AS IdleDuration,
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, SUM(CASE WHEN j.StatusType = 'PIERCING' THEN CAST(j.Duration AS float) ELSE 0 END), 0), 108), 
        '00:00:00'
    ) AS PiercingDuration,
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, SUM(CASE WHEN j.StatusType = 'PREHEATING' THEN CAST(j.Duration AS float) ELSE 0 END), 0), 108), 
        '00:00:00'
    ) AS PreheatingDuration,
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, SUM(CASE WHEN j.StatusType = 'POSITIONING' THEN CAST(j.Duration AS float) ELSE 0 END), 0), 108), 
        '00:00:00'
    ) AS PositioningDuration,
    -- PlannedTime przekształcone na format czasu
    ISNULL(
        CONVERT(varchar, DATEADD(SECOND, CAST(planned.PlannedTime AS float), 0), 108), 
        '00:00:00'
    ) AS PlannedTimeFormatted,
    st.Status OverallStatus
FROM 
    PartCheck.dbo.Jobtable j2
CROSS APPLY 
    OPENJSON(j2.msg, '$.States') WITH (
        StatusType nvarchar(50) '$.Status.StatusType',
        Duration float '$.Duration'
    ) AS j
CROSS APPLY 
    OPENJSON(j2.msg, '$.Plans[0].Data') WITH (
        PlannedTime float '$.PlannedTime'
    ) AS planned
CROSS APPLY 
	OPENJSON(j2.msg, '$.Plans[0]') 
	WITH (
        Status nvarchar(20) '$.Status'
    ) AS st
WHERE
    DATEADD(hour, 2, j2.[_internal_timestamp]) >= '2024-09-03'
    AND DATEADD(hour, 2, j2.[_internal_timestamp]) < DATEADD(DAY, 1, '2024-09-03')
GROUP BY 
st.Status,
	JSON_VALUE(j2.msg, '$.PartProgramName'),
	j2.[_internal_timestamp],
	j2.[_internal_endtime],
    planned.PlannedTime,
    j2.id
ORDER BY 
    j2.[_internal_timestamp] DESC;

";

        $datas = sqlsrv_query($conn, $sql);
        $dataresult = [];

        while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
            $dataresult[] = $row;
        }
        ?>
        <div class="table-responsive">
            <form id="dateForm" method="post" action="">
                <label for="selected_date">Wybierz datę:</label>
                <input type="date" id="selected_date" name="selected_date" value="<?php echo htmlspecialchars($formattedDate); ?>" onchange="submitForm()" required>
            </form>
            <table class="table table-sm table-hover table-striped table-bordered" id="mytable"
                style="font-size: calc(9px + 0.390625vw)">
                <thead>
                    <th>Program<br />
                        <div class="search-container">
                            <input type="text" id="searchProgram" onkeyup="searchTable()" placeholder="Szukaj programu...">
                        </div>
                    </th>
                    <th>Szczegóły pracy</th>
                    <th>Status<br /><select id="searchStatus" onchange="searchTable()">
                            <option value="">ALL</option>
                            <option value="In Progress">In Progress</option>
                            <option value="COMPLETED">COMPLETED</option>
                            <option value="UNKNOWN">UNKNOWN</option>
                        </select></th>
                    <th>Czas</th>
                    <th>Parametry</th>
                </thead>
                <tbody class="row_position">

                    <?php
                    $servername = "10.100.100.27";
                    $username = "root";
                    $password = "kHLqB9AN-liWJUODhycsCYIPdUw";
                    $dbname = "mdc";

                    // Tworzenie połączenia
                    $conn1 = new mysqli($servername, $username, $password, $dbname);

                    // Sprawdzanie połączenia
                    if ($conn1->connect_error) {
                        die("Connection failed: " . $conn1->connect_error);
                    }

                    $sql = "SELECT 
    `_internal_timestamp` as time,
    LEFT(
        SUBSTRING(
            JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename')), 
            LENGTH(JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename'))) - LOCATE('/', REVERSE(JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename')))) + 2, 
            LENGTH(JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename')))
        ), 
        LENGTH(SUBSTRING(
            JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename')), 
            LENGTH(JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename'))) - LOCATE('/', REVERSE(JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename')))) + 2, 
            LENGTH(JSON_UNQUOTE(JSON_EXTRACT(msg, '$.Filename')))
        )) - 4
    ) AS PartProgramName
FROM 
    db_eventpartprogramtable
ORDER BY 
    id DESC
LIMIT 1;
";

                    $result = $conn1->query($sql);

                    // Sprawdź czy udało się wykonać zapytanie
                    if (!$result) {
                        die('Błąd wykonania zapytania: ' . $conn1->error);
                    }

                    $date = new DateTime($formattedDate);

                    // Tworzymy obiekt DateTime dla dzisiejszej daty
                    $today = new DateTime();

                    // Przetwarzaj wyniki zapytania
                    if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['PartProgramName'] . "</td>";
                            echo "<td></td>";
                            echo "<td>In Progress</td>";
                            echo "<td>";
                            echo "Czas rozpoczęcia:     ";
                            if (isset($row['time'])) {
                                // Sprawdź, czy wartość jest instancją DateTime
                                if ($row['time'] instanceof DateTime) {
                                    // Dodaj 2 godziny
                                    $row['time']->modify('+2 hours');
                                    echo $row['time']->format('Y-m-d H:i:s');
                                } else {
                                    // Jeśli to nie jest DateTime, przekształć to w DateTime i dodaj 2 godziny
                                    $time = new DateTime($row['time']);
                                    $time->modify('+2 hours');
                                    echo $time->format('Y-m-d H:i:s');
                                }
                            } else {
                                echo '';
                            }
                            echo "</td>";
                            echo "<td></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    <?php foreach ($dataresult as $data) : ?>
                        <tr>
                            <td>
                                <?php echo isset($data['PartProgramName']) ? $data['PartProgramName'] : ''; ?>
                            </td>
                            <td>
                                <div class="container">
                                    <div class="columnleft">
                                        <?php
                                        echo "Cięcię: " . $data['CuttingDuration'] . "<br />";
                                        echo "Błąd: " . $data['ErrorDuration'] . "<br />";
                                        echo "Bezczynność: " . $data['IdleDuration'];
                                        ?>
                                    </div>
                                    <div class="columnright">
                                        <?php
                                        echo "Przewiercanie: " . $data['PiercingDuration'] . "<br />";
                                        echo "Podgrzewanie: " . $data['PreheatingDuration'] . "<br />";
                                        echo "Pozycjonowanie: " . $data['PositioningDuration'];
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php echo isset($data['OverallStatus']) && $data['OverallStatus'] !== '' ? $data['OverallStatus'] : ''; ?>
                            </td>
                            <td>
                                <?php
                                echo "Czas rozpoczęcia:     ";
                                echo isset($data['Starttime'])
                                    ? ($data['Starttime'] instanceof DateTime
                                        ? $data['Starttime']->format('Y-m-d H:i:s')
                                        : $data['Starttime'])
                                    : '';
                                echo "<br />";
                                ?>
                                <?php
                                echo "Czas zakończenia:     ";
                                echo isset($data['Endtime'])
                                    ? ($data['Endtime'] instanceof DateTime
                                        ? $data['Endtime']->format('Y-m-d H:i:s')
                                        : $data['Endtime'])
                                    : '';
                                echo "<br />";
                                ?>
                            </td>
                            <td>
                                <?php
                                if (!function_exists('timeToSeconds')) {
                                    function timeToSeconds($time)
                                    {
                                        list($hours, $minutes, $seconds) = explode(':', $time);
                                        return ($hours * 3600) + ($minutes * 60) + $seconds;
                                    }
                                }

                                if (!function_exists('secondsToTime')) {
                                    function secondsToTime($seconds)
                                    {
                                        $hours = floor($seconds / 3600);
                                        $minutes = floor(($seconds % 3600) / 60);
                                        $seconds = $seconds % 60;
                                        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                    }
                                }

                                $cuttingDuration = timeToSeconds($data['CuttingDuration']);
                                $piercingDuration = timeToSeconds($data['PiercingDuration']);
                                $preheatingDuration = timeToSeconds($data['PreheatingDuration']);
                                $positioningDuration = timeToSeconds($data['PositioningDuration']);
                                $errorDuration = timeToSeconds($data['ErrorDuration']);
                                $idleDuration = timeToSeconds($data['IdleDuration']);
                                $PlannedTimeFormatted = timeToSeconds($data['PlannedTimeFormatted']);
                                $totalWorkTime = $cuttingDuration + $piercingDuration + $preheatingDuration + $positioningDuration;
                                $totalIdleTime = $errorDuration + $idleDuration;

                                echo "Czas pracy: " . secondsToTime($totalWorkTime) . "<br />";
                                echo "Planowany czas: " . secondsToTime($PlannedTimeFormatted) . "<br />";
                                echo "Czas Bezczynności: " . secondsToTime($totalIdleTime) . "<br />";
                                ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php include 'globalnav.php'; ?>
</body>
<script>
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue, statusFilter, status, programFilter;

        // Pobierz wartość z wyszukiwania w Programie
        programFilter = document.getElementById("searchProgram").value.toUpperCase();

        // Pobierz wartość z wyszukiwania w Statusie
        statusFilter = document.getElementById("searchStatus").value.toUpperCase();

        table = document.getElementById("mytable");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            tr[i].style.display = ""; // Domyślnie pokaż wiersz

            // Filtruj po programie
            if (programFilter) {
                td = tr[i].getElementsByTagName("td")[0]; // Indeks kolumny Program
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(programFilter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                        continue; // Przejdź do następnego wiersza
                    }
                }
            }

            // Filtruj po statusie
            if (statusFilter) {
                td = tr[i].getElementsByTagName("td")[2]; // Indeks kolumny Status (zakładamy, że jest w 4 kolumnie)
                if (td) {
                    // Usuń białe znaki z tekstu statusu
                    txtValue = (td.textContent || td.innerText);
                    if (txtValue.toUpperCase().indexOf(statusFilter) > -1) {
                        tr[i].style.display = ""; // Pokaż wiersz, jeśli status pasuje
                    } else {
                        tr[i].style.display = "none"; // Ukryj wiersz, jeśli status nie pasuje
                    }
                }
            }
        }
    }
</script>

</html>