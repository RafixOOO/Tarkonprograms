<?php require_once '../auth.php'; ?>

<?php
require_once 'vendor/autoload.php';

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;

// Now you can use the Utils class

$programs = isset($_GET['programs']) ? (array)$_GET['programs'] : ['cutlogic'];
$myVariable = isset($_GET['myCheckbox']) ? 1 : 0;
$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
$keywordArray = explode(' ', $keywords);
$dataFrom = isset($_GET['dataFrom']) ? $_GET['dataFrom'] : '';
$dataTo = isset($_GET['dataTo']) ? $_GET['dataTo'] : '';
$filesToLoad = [
    'cutlogic' => 'cutlogicsql.php',
    'inne' => 'othersql.php',
    'messer' => 'messer.php',
    'v630' => 'v630.php',
];

$filteredData = [];

foreach ($programs as $program) {
    if (isset($filesToLoad[$program])) {
        require_once $filesToLoad[$program];
        while ($rowData = sqlsrv_fetch_array($data, SQLSRV_FETCH_ASSOC)) {
            // Dodaj dane do tablicy tylko jeśli spełniają warunki filtrowania
            if (checkData($rowData, $myVariable, $keywordArray, $dataFrom, $dataTo)) {
                $filteredData[] = $rowData;
            }
        }
    }
}

function checkData($item, $myVariable, $keywordArray, $dataFrom, $dataTo)
{
    if (
        $myVariable == 0 && ($keywordArray == '') &&
        (
            ($item['ilosc'] == 0 || $item['ilosc'] == '')
            ? ($item['ilosc_zrealizowana'] >= $item['amount_order'] || $item['lok'] == 1)
            : ($item['ilosc_zrealizowana'] >= $item['ilosc'] || $item['lok'] == 1)
        )
    ) {
        return false;
    }


    if ($dataFrom !== '') {
        $dataFrom = new DateTime($dataFrom);
        $itemData = $item['data'] instanceof DateTime ? $item['data'] : new DateTime($item['data']);
        if ($itemData < $dataFrom) {
            return false;
        }
    }

    if ($dataTo !== '') {
        $dataTo = new DateTime($dataTo);
        $itemData = $item['data'] instanceof DateTime ? $item['data'] : new DateTime($item['data']);
        if ($itemData > $dataTo) {
            return false;
        }
    }

    foreach ($keywordArray as $keyword) {
        $keyword = trim($keyword);
        $columnsToSearch = ['zespol', 'Detal', 'maszyna', 'wykonal', 'cutlogic'];
        $matchesKeyword = false;

        foreach ($columnsToSearch as $column) {
            if ($myVariable == 0 && (($item['ilosc'] == 0 || $item['ilosc'] == '')
                ? ($item['ilosc_zrealizowana'] >= $item['amount_order'] || $item['lok'] == 1)
                : ($item['ilosc_zrealizowana'] >= $item['ilosc'] || $item['lok'] == 1)
            )) {
                continue;
            }

            $columnValue = $item[$column] instanceof DateTime ? $item[$column]->format('Y-m-d H:i:s') : $item[$column];

            if (stripos($columnValue, $keyword) !== false) {
                $matchesKeyword = true;
                break;
            }
        }

        if (!$matchesKeyword) {
            return false;
        }
    }

    return true;
}

$pageSizeOptions = [50, 200, 500, 1000];
$pageSize = isset($_GET['page_size']) ? $_GET['page_size'] : 50;
$pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
$showAll = $pageSize == count($filteredData); // Sprawdzamy, czy wartość jest równa -1, aby określić, czy "ALL" jest wybrane

if ($showAll) {
    $pageSize = count($filteredData);
} else {
    $pageSize = (int)$pageSize;
    $pageSize = max(1, $pageSize); // Upewniamy się, że $pageSize jest większe lub równe 1
}
$adapter = new ArrayAdapter($filteredData);
$pagerfanta = new Pagerfanta($adapter);
$pagerfanta->setMaxPerPage($pageSize);
$pagerfanta->setCurrentPage($pageNumber);

$currentPageResults = $pagerfanta->getCurrentPageResults();

$sumaIlosc = array_sum(array_column($filteredData, 'ilosc'));
$sumaIloscZrealizowana = array_sum(array_column($filteredData, 'ilosc_zrealizowana'));
try {
    $sumaKolumnyJeden = 0;
    foreach ($filteredData as $row) {
        if ($row['lok'] == 1) {
            // Sumowanie kolumny gdy kolumna 'lok' jest równa 1
            $sumaKolumnyJeden = $sumaKolumnyJeden + $row['ilosc_zrealizowana'];
        }
    }
} catch (error) {
    $sumaKolumnyJeden = 0;
}

$jsonData = json_encode($filteredData);



?>
<!DOCTYPE html>

<html>

<head>

    <?php require_once('globalhead.php');
    ?>
    <style>
        .bottom-banner1 {
            background-color: orange;
            position: fixed;
            bottom: 3%;
            right: 35%;
            font-size: 18px;
            width: 15%;
            text-align: center;
            border-radius: 10px;
        }

        .verticalrotate {
            position: fixed;
            bottom: 50%;
            left: 84.5%;
            width: 30%;
            transform: rotate(-90deg);
        }

        #loader-wrapper {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: #ECF0F1;
            display: none;
        }

        .js .load,
        .js #loader-wrapper {
            display: block;
        }

        .label {
            /*from  w  ww. ja  v  a 2  s  .  co  m*/
            text-align: center;
            width: 600px;
            font-size: 20px;
            font-weight: bold;
            margin: 20px;
        }

        /* Styl dla spinnera */
        #loadingIndicator {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Wycentruj spinner */
            z-index: 10000;
            /* Zadaj jeszcze wyższy indeks warstwy, aby spinner był na wierzchu */
            display: none;
            /* Ukryj początkowo spinner */
        }

        /* Dodatkowe style dla większego spinnera */
        .spinner-border {
            width: 6rem;
            /* Szerokość spinnera */
            height: 6rem;
            /* Wysokość spinnera */
            border-width: 0.5em;
            /* Grubość obramowania */
        }

        .input-group1 {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        .input-group1 .fields {
            display: flex;
            gap: 10px; /* Space between input fields */
            margin-bottom: 10px;
        }
        .input-group1 .fields .form-control {
            width: 100%; /* Adjust width as needed */
            text-align: center;
        }
        .input-group1 label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block; /* Ensure labels are displayed above input fields */
        }
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            margin-top: 10px;
            justify-items: center; /* Center items horizontally */
        }
        .keypad button {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 18px;
            cursor: pointer;
        }
        .keypad .btn-zero {
            grid-column: span 2; /* Przyciski 0 zajmują dwie kolumny */
        }
        .keypad .btn-delete {
            grid-column: span 2; /* Przyciski usuwania zajmują dwie kolumny */
            background-color: #f00;
            color: #fff;
        }
        .keypad .btn-delete:hover {
            background-color: #c00;
        }
    </style>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <!-- 2024 Created by: Rafał Pezda-->
    <!-- link: https://github.com/RafixOOO -->
    <div class="container-fluid" style="width:90%;margin-left:auto;margin-right:auto;">
        <?php if (!isLoggedIn()) { ?>
            <div class="progress verticalrotate">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
            </div>
        <?php } ?>


        <div class="mb-3" style="float:right;">

            <form id="myForm1" method="get" action="">
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" name="keywords" value="<?php echo $keywords; ?>" placeholder="Nazwa..." autofocus>

                    <button class="btn btn-primary" type="submit">Szukaj</button>
                    <a href="detale.php">
                        <button class="btn btn-secondary form-control form-control-lg" type="button">Wyczyść</button>
                    </a>
                    <br /><br />
                </div>
                <div style="text-align:right;">
                    <br />
                    <select data-placeholder="Wybierz kategorie" class="chosen-select form-control form-control-lg" name="programs" style="float:right; width: 65%;">
                        <option value="cutlogic" <?php echo in_array("cutlogic", $programs) ? 'selected' : ''; ?>>CUTLOGIC</option>
                        <option value="messer" <?php echo in_array("messer", $programs) ? 'selected' : ''; ?>>MESSER</option>
                        <option value="v630" <?php echo in_array("v630", $programs) ? 'selected' : ''; ?>>V630</option>
                    </select><br /><br /><br />
                    od: <input type="date" value="<?php echo $dataFrom; ?>" name="dataFrom"> do: <input type="date" value="<?php echo $dataTo; ?>" name="dataTo">
                </div>
        </div>
        <div class="form-group" style="float:left;">
            <label for="pageSizeSelect">Liczba wyników na stronie:</label>
            <select class="form-control" id="pageSizeSelect" name="page_size">
                <?php foreach ($pageSizeOptions as $option) : ?>
                    <option value="<?php echo $option; ?>" <?php echo $pageSize === $option ? 'selected' : ''; ?>>
                        <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="checkbox">Pokaż zakończone: </label>
            <input type="checkbox" name="myCheckbox" id="checkbox" <?php if ($myVariable == 1) echo 'checked'; ?>>
        </div>
        </form>

        <div style="clear:both;"></div>
        <div class="table-responsive">
            <table id="myTable" class="table table-sm table-hover table-striped table-bordered" style="font-size: calc(9px + 0.390625vw)">

                <caption style="caption-side: top;"><?php echo $_SESSION['project_name']; ?></caption>
                <thead>
                    <tr>
                        <th scope="col" style="width:10em;">Zespół</th>
                        <th scope="col" style="width:10em;">Nazwa programu</th>
                        <th scope="col">Detal</th>
                        <th scope="col">Ilość Zaplanowana / Zrobiona</th>
                        <th scope="col">V200</th>
                        <th scope="col">Wymiar</th>
                        <th scope="col">Materiał</th>
                        <th scope="col">Długość</th>
                        <th scope="col">Dlugość zrealizowana</th>
                        <th scope="col">Waga</th>
                        <th scope="col">Waga zrealizowana</th>
                        <th scope="col">Opis</th>
                        <th scope="col">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentPageResults as $data) :
                        $ilosc = '';
                        if ($data['ilosc'] == 0 or $data['ilosc'] == '') {
                            $ilosc = $data['amount_order'];
                            $szer = $data['ilosc_zrealizowana'] / $ilosc * 100;
                        } else {
                            $szer = $data['ilosc_zrealizowana'] / $data['ilosc'] * 100;
                            $ilosc = $data['ilosc'];
                        }

                        if ($data['lok'] == 1 or $szer >= 100) {
                            if ($data['lok'] == 1) {
                                echo "<tr class='table-danger'>";
                            } else {
                                echo "<tr>";
                            }
                        } else if (($data['maszyna'] == "" or $data['maszyna'] == "Recznie" or $data['maszyna'] == "Kooperacyjnie" or $data['maszyna'] == "Pila") and $szer < 100) {
                            echo '<tr id="myRow" onclick="handleClick(this);">';
                        }
                    ?>
                        <td id="zespol"><?php if ($data['status'] == 1) {
                                            echo $data['zespol'] . " <img src='../static/triangle.svg' /></img>";
                                        } else {
                                            echo $data['zespol'];
                                        } ?></td>
                        <td id="Program1"><?php echo $data['cutlogic']; ?></td>
                        <td id="detal"><?php echo $data['Detal']; ?></td>
                        <td>
                            <center><?php echo $ilosc; ?>/<?php echo $data['ilosc_zrealizowana']; ?></center><br />
                            <div class="progress" style="height:25px;font-size: 16px;">
                                <?php if ($szer <= 100) { ?>
                                    <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo $data['ilosc_zrealizowana']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $ilosc; ?>'></div>
                                    <span class='progress-bar bg-white text-dark' style='width:
                            <?php if (100 - $szer < 0) {
                                        echo 0;
                                    } else {
                                        echo 100 - $szer;
                                    } ?>%;'> </span>
                                <?php } else { ?>
                                    <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo $data['ilosc_zrealizowana']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $ilosc; ?>'></div>
                                <?php }
                                ?>
                        </td>
                        <td><?php echo $data['ilosc_v200'] . "/" . $data['ilosc_v200_zre']; ?></td>
                        <td><?php echo $data['profil']; ?></td>
                        <td><?php echo $data['material']; ?></td>
                        <td><?php echo $data['dlugosc']; ?></td>
                        <td><?php echo $data['dlugosc_zre']; ?></td>
                        <td><?php echo $data['Ciezar']; ?></td>
                        <td><?php echo $data['Calk_ciez']; ?></td>
                        <td><?php echo $data['uwaga'] . " " . $data['wykonal']; ?></td>
                        <td><?php if ($data['data'] != "") {
                                echo $data['data']->format('Y-m-d H:i:s');
                            } ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <div id="loadingIndicator" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div style="float: right;">
                <?php
                $view = new TwitterBootstrap4View();
                $options = array(
                    'prev_message' => '<',
                    'next_message' => '>',
                    'routeGenerator' => function ($page) {
                        $queryString = $_SERVER['QUERY_STRING'];
                        parse_str($queryString, $queryParams);
                        $queryParams['page'] = $page;
                        $newQueryString = http_build_query($queryParams);
                        $url = $_SERVER['PHP_SELF'] . '?' . $newQueryString;
                        return $url;
                    },
                );

                echo $view->render($pagerfanta, $options['routeGenerator'], $options);
                ?>
            </div>
            <div class="btn-toolbar position-fixed" role="toolbar" aria-label="Toolbar with button groups" style="bottom:4%;">
                <div class="btn-group me-2 " role="group" aria-label="First group">

                    <?php if (!isUserParts()) { ?>
                        <?php if (!isUserPartsKier()) { ?>
                            <button type="button" onclick="localStorage.removeItem('number1'); window.location.href = 'main.php';" class="btn btn-warning btn-lg">Wyjdź
                            </button>
                        <?php } ?>
                        <?php if (isUserPartsKier()) { ?>
                            <button type="Submit" onclick="localStorage.removeItem('number1');window.location.href = 'statuschange.php';" class="btn btn-warning btn-lg" name="role" value="role_parts">Wyjdź
                            </button>
                            <button type="button" onclick="localStorage.removeItem('number1'); location.reload();" class="btn btn-warning btn-lg">Przełącz
                            </button>
                    <?php }
                    } ?>
                    <?php if (isUserPartsKier() && isUserParts()) { ?>
                        <button type="button" onclick="localStorage.removeItem('number1');window.location.href = 'statuschange.php';" class="btn btn-warning btn-lg" name="role" value="role_parts">Przełącz
                        </button>
                        <button type="button" onclick="sendSelectedRowsToPHP2()" class="btn btn-warning btn-lg">
                            Kooperacyjnie
                        </button>

                    <?php } ?>


                </div>
                <div class="btn-group me-2" role="group" aria-label="Second group">
                    <?php if (!isUserParts()) { ?>
                        <?php if (in_array("inne", $programs)) { ?>
                            <button type="button" onclick="sendSelectedRowsToPHP()" class="btn btn-warning btn-lg">Recznie
                            </button>
                        <?php } ?>
                        <button type="button" onclick="sendSelectedRowsToPHP1()" class="btn btn-warning btn-lg">Pila
                        </button>
                        <?php if (in_array("cutlogic", $programs)) { ?>
                            <div>
                                <button type="button" onclick="selectAllRows()" class="btn btn-warning btn-lg">Zaznacz
                                    wiele
                                </button>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if (isUserParts()) { ?>
                        <button type="button" onclick="status()" class="btn btn-warning btn-lg">Status</button>
                    <?php } ?>
                </div>
            </div>
            <br /><br />

            <div class="modal fade" id="mymodal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edycja Detalu</h4>
                        </div>
                        <form method="POST" action="zapisze_dane.php" id="myForm">
                            <div class="modal-body">
                                Nazwa projektu: <label id="projectName" name="projectName"></label><br />
                                <input type="hidden" name="project">
                                Zespół: <label id="zespolName" name="zespolName"></label><br />
                                Detal: <label id="detalName" name="detalName"></label><br />
                                <input type="hidden" name="detal">
                                Numer pracownika: <label id="numerName" name="numerName"></label>
                                <input type="hidden" name="numer">
                                <br />

                                <?php if (!isUserParts()) { ?>
                                    <div class="input-group1">
                    <div class="fields">
                        <div>
                            <label for="ilosc">Ilość</label>
                            <input id="ilosc" class="form-control" type="number" inputmode="numeric" placeholder="Ilość" name="ilosc">
                        </div>
                        <div>
                            <label for="dlugosc">Długość</label>
                            <input id="dlugosc" class="form-control" type="number" inputmode="numeric" placeholder="Długość" name="dlugosc">
                        </div>
                    </div>
                </div>
                                    <div class="keypad">
                                        <button type="button" class="key btn-light" data-value="1">1</button>
                                        <button type="button" class="key btn-light" data-value="2">2</button>
                                        <button type="button" class="key btn-light" data-value="3">3</button>
                                        <button type="button" class="key btn-light" data-value="4">4</button>
                                        <button type="button" class="key btn-light" data-value="5">5</button>
                                        <button type="button" class="key btn-light" data-value="6">6</button>
                                        <button type="button" class="key btn-light" data-value="7">7</button>
                                        <button type="button" class="key btn-light" data-value="8">8</button>
                                        <button type="button" class="key btn-light" data-value="9">9</button>
                                        <button type="button" class="key btn-light" data-value="0">0</button>
                                        <button type="button" class="btn-delete">-</button>
                                    </div>
                                    <br />
                                    <select class="form-control" name="maszyna" required>
                                        <option value="Recznie">Recznie</option>
                                        <option value="Pila" selected>Pila</option>
                                    </select>
                                <?php } ?>
                            </div>
                            <div class="modal-footer">
                                <?php
                                if (!isUserParts()) { ?>
                                    <button type="Submit" name="save" class="btn btn-default" value='piece'>Zapisz</button>
                                <?php }
                                ?>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="programmodal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Zaznaczone programy</h4>
                        </div>
                        <div class="modal-body">
                            <ul id="selectedProgramsList"></ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
    <div class="modal" id="user-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Weryfikacja użytkownika</h5>
                </div>
                <div class="modal-body">
                    <form id="user-form">
                        <div class="form-group">
                            <label for="user-number">Wprowadź swój numer:</label>
                            <?php
                            if (isUserPartsKier()) {
                                $kiersql = "Select * from dbo.Persons where [user]='' and [prac_parts]=1";
                                $stmt = sqlsrv_query($conn, $kiersql);
                            ?> <select type="text" class="form-control" id="user-number" name="user-number" required>
                                    <?php
                                    while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                    ?>
                                        <option value="<?php echo $data['identyfikator']; ?>" data-imie-nazwisko="<?php echo $data['imie_nazwisko']; ?>"><?php echo $data['imie_nazwisko']; ?></option>

                                    <?php }
                                    ?>
                                </select> <?php

                                        } else if (!isUserPartsKier()) { ?>
                                <input type="number" class="form-control" id="user-number" name="user-number">
                            <?php } ?>
                        </div>

                        <div class="modal-footer">
                            <?php
                            if (isUserPartsKier()) { ?>
                                <button id="submit-button" class="btn btn-default">Przejdź</button>
                            <?php } else if (!isUserPartsKier()) { ?>
                                <a href="http://localhost/programs/Tarkonprograms/parts/dozrobienia.php?keywords=<?php echo $_SESSION['project_name']; ?>&toggleButtons=Projekt" class="btn btn-default">Wykonane detale</a>
                                <a href="main.php" class="btn btn-default">Projekty</a>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if (!isLoggedIn()) { ?>
        <link rel="stylesheet" href="../assets/css/plugins.min.css" />
        <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
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
    <?php if (isUserPartsKier()) { ?>
        <div id="myElement" class="bottom-banner1"></div>
    <?php } ?>

    </div>
    <?php if (isLoggedIn()) { ?>
        <?php require_once('globalnav.php') ?>
    <?php } ?>
</body>
<script>
    let currentInput = null;

    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', () => {
            currentInput = input;
        });
    });

    document.querySelectorAll('.key').forEach(button => {
        button.addEventListener('click', () => {
            if (currentInput) {
                currentInput.value += button.getAttribute('data-value');
            }
        });
    });

    document.querySelector('.btn-delete').addEventListener('click', () => {
        if (currentInput) {
            currentInput.value = currentInput.value.slice(0, -1); // Remove last character
        }
    });

    $(document).ready(function() {
        // Obsługa zdarzenia zmiany checkboxa
        $('#checkbox').change(function() {
            // Wyślij formularz po zaznaczeniu lub odznaczeniu checkboxa
            $('#myForm1').submit();
        });

        // Obsługa zdarzenia zmiany pola select
        $('#pageSizeSelect').change(function() {
            // Wyślij formularz po zmianie wartości w polu select
            $('#myForm1').submit();
        });
    });

    $('html').addClass('js');


    $(window).on("load", function() {
        $("#loader-wrapper").fadeOut();
    });

    var currentPage = <?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>;

    const pageItems = document.querySelectorAll('.pagination li');

    // Iteracja przez każdy element li i usunięcie słowa "Current"
    pageItems.forEach(function(item) {
        if (item.classList.contains('active')) {
            item.querySelector('.page-link').innerHTML = currentPage;
        }
    });

    var clicks = 0;
    var timeout;

    function handleClick(row) {
        clicks++;

        if (clicks === 1) {
            timeout = setTimeout(function() {
                singleClickAction(row);
                clicks = 0;
            }, 200);
        } else if (clicks === 2) {
            clearTimeout(timeout);
            doubleClickAction(row);
            clicks = 0;
        }
    }

    var selectedrow = [];

    function singleClickAction(row) {
        var hasClass = row.classList.contains("table-warning");
        if (hasClass) {
            row.classList.remove("table-warning");
            removeRowFromSelected("<?php echo $_SESSION['project_name']; ?>" + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
        } else {
            row.classList.add("table-warning");
            addRowToSelected("<?php echo $_SESSION['project_name']; ?>" + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
        }
    }

    function addRowToSelected(row) {
        selectedrow.push(row);
    }

    function getColumnData(row, columnId) {
        var columnElement = row.querySelector('#' + columnId);
        return columnElement.innerText;
    }

    function removeRowFromSelected(row) {
        var index = selectedrow.indexOf(row);
        if (index !== -1) {
            selectedrow.splice(index, 1);
        }
    }

    function selectAllRows() {
        var tableRows = document.querySelectorAll('#myTable tbody tr');
        var allSelected = true; // Zmienna do śledzenia, czy wszystkie wiersze są już zaznaczone
        var program1Values = new Set(); // Zbiór do przechowywania unikalnych wartości z kolumny "program1"

        tableRows.forEach(function(row) {
            var hasClass = row.classList.contains("table-warning");
            if (!hasClass) {
                allSelected = false; // Ustaw, że nie wszystkie wiersze są zaznaczone
                row.classList.add("table-warning");
                addRowToSelected("<?php echo $_SESSION['project_name']; ?>" + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
                program1Values.add(getColumnData(row, "Program1"));
            }
        });

        // Jeśli wszystkie wiersze są już zaznaczone, to odznacz je
        if (allSelected) {
            tableRows.forEach(function(row) {
                row.classList.remove("table-warning");
                removeRowFromSelected("<?php echo $_SESSION['project_name']; ?>" + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
            });
        }

        // Sprawdź unikalne wartości kolumny "program1"
        if (program1Values.size > 1) {
            // Wyświetl okno modalne
            showModalDialog(program1Values);
        }
    }


    function showModalDialog(program1Values) {
        var modal = $('#programmodal');
        var selectedProgramsList = $('#selectedProgramsList');

        // Wyczyść listę programów przed dodaniem nowych
        selectedProgramsList.empty();

        // Dodaj zaznaczone programy do listy
        program1Values.forEach(function(program) {
            selectedProgramsList.append('<li>' + program + '</li>');
        });

        // Wyświetl okno modalne
        modal.modal('show');
    }

    function doubleClickAction(row) {
        var projectName = "<?php echo $_SESSION['project_name']; ?>";
        var zespolName = row.querySelector('#zespol').innerHTML;
        var detalName = row.querySelector('#detal').innerHTML;

        var projectNameDiv = document.querySelector('#mymodal #projectName');
        var zespolNameDiv = document.querySelector('#mymodal #zespolName');
        var detalNameDiv = document.querySelector('#mymodal #detalName');
        var numerNameDiv = document.querySelector('#mymodal #numerName');

        projectNameDiv.innerHTML = projectName;
        zespolNameDiv.innerHTML = zespolName;
        detalNameDiv.innerHTML = detalName;
        numerNameDiv.innerHTML = localStorage.getItem('number1');

        document.getElementById("myForm").elements.namedItem("project").setAttribute("value", projectName);
        document.getElementById("myForm").elements.namedItem("detal").setAttribute("value", detalName);
        document.getElementById("myForm").elements.namedItem("numer").setAttribute("value", localStorage.getItem('number1'));

        $('#mymodal').modal('show');
    }

    function sendSelectedRowsToPHP() {
        var xhr = new XMLHttpRequest();
        var url = 'zakoncz.php';
        var params = 'selectedrow=' + JSON.stringify(selectedrow);

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Odpowiedź z serwera
                console.log(xhr.responseText);
                location.reload();
            }
        };

        xhr.send(params);
    }

    function sendSelectedRowsToPHP1() {
        var xhr = new XMLHttpRequest();
        var url = 'zakoncz1.php';
        var params = 'selectedrow=' + JSON.stringify(selectedrow);

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Odpowiedź z serwera
                console.log(xhr.responseText);
                location.reload();
            }
        };

        xhr.send(params);
    }

    function sendSelectedRowsToPHP2() {
        var xhr = new XMLHttpRequest();
        var url = 'zakoncz2.php';
        var params = 'selectedrow=' + JSON.stringify(selectedrow);

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Odpowiedź z serwera
                console.log(xhr.responseText);
                location.reload();
            }
        };

        xhr.send(params);
    }

    function status() {
        var xhr = new XMLHttpRequest();
        var url = 'status.php';
        var params = 'selectedrow=' + JSON.stringify(selectedrow);

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Odpowiedź z serwera
                console.log(xhr.responseText);
                location.reload();
            }
        };

        xhr.send(params);
    }
</script>
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
                    location.reload();
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
<?php if (!isUserParts()) { ?>
    <script>
        var stored;
        var nazwa;
        $(document).ready(function() {
            stored = localStorage.getItem('number1');
            nazwa = localStorage.getItem('nazwa');
            if (stored) {
                // Numer został już poprawnie sprawdzony, nie wyświetlamy okna dialogowego
                console.log('Numer został już sprawdzony: ' + stored);
                toastr.success('Weryfikacja przebiegła pomyślnie!!!<br/> Witaj ' + nazwa);
                try {
                    document.getElementById('myElement').innerHTML = "Pracujesz w kontekście <br>" + nazwa;
                } catch (error) {
                    console.error();
                }

            } else {
                // Numer nie został jeszcze sprawdzony, wyświetlamy okno dialogowe
                $('#user-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#user-modal').modal('show');
                $('#user-number').focus();


                $('#user-modal').on('shown.bs.modal', function() {
                    selectInput();
                });


                $('#user-number').on('input', function() {
                    var userNumber = $(this).val();


                    $('#user-number').on('keypress', function(e) {
                        if (e.which === 13) {
                            e.preventDefault();
                            var userNumber = $(this).val();
                            if (userNumber.length === 10) {
                                sendForm(userNumber);
                            } else {
                                console.log('Wprowadź dokładnie 10 cyfr.');
                            }
                        }
                    });
                });

                $('#submit-button').on('click', function(e) {
                    e.preventDefault(); // Zapobiegamy domyślnemu zachowaniu przycisku (np. przeładowaniu strony)
                    var userNumber = $('#user-number').val();
                    if (userNumber.length === 10) {
                        sendForm(userNumber);
                    } else {
                        console.log('Wprowadź dokładnie 10 cyfr.');
                    }
                });

                function sendForm(userNumber) {
                    $.ajax({
                        url: 'check_person.php',
                        type: 'POST',
                        data: {
                            number: userNumber
                        },
                        success: function(response) {
                            var czesci = response.split(",")
                            console.log(response);
                            if (czesci[0] === 'true') {
                                console.log('Twój numer znajduje się w bazie danych!');
                                localStorage.setItem('number1', userNumber);
                                localStorage.setItem('nazwa', czesci[1]);
                                localStorage.setItem('czas', 0);
                                location.reload();
                            } else {
                                console.log('Twój numer nie został odnaleziony w bazie danych.');
                                location.reload();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Wystąpił błąd podczas sprawdzania numeru w bazie danych.');
                            location.reload();
                            console.log(jqXHR.responseText);
                        },
                        complete: function() {
                            $('#user-modal').modal('hide');
                        }
                    });
                }
            }
        });
    </script>
<?php } ?>

</html>