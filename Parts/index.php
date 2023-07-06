
<!DOCTYPE html>

<html>

<head>

  <?php require_once('globalhead.php') ;
  require_once('../auth.php');
  ?>
  <style>

    .bottom-banner1 {
  background-color: orange;
  position: fixed;
  top: 8px;
  right: 16px;
  font-size: 18px;
  width:8%;
  text-align: center;
border-radius: 10px;
}

.verticalrotate{
  position:fixed;
  bottom:50%;
  left:84.5%;
  width: 30%;
  transform: rotate(-90deg);
}

  </style>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<script defer src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script defer src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark">


  <div class="container-fluid">
    <?php if(!isLoggedIn()){ ?>
  <div class="progress verticalrotate">
  <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
</div>
    <?php } ?>

    <?php require_once('globalnav.php') ?>
    <table id="myTable" class="table table-striped table-bordered">


      <thead>
        <tr>
          <th scope="col">Projekt</th>
          <th scope="col" style="width:10em;">Zespoły</th>
          <th scope="col">Detal</th>
          <th scope="col">Ilosc Wymagana/Zrealizowana</th>
          <th scope="col">V200</th>
          <th scope="col">Maszyna</th>
          <th scope="col">Wymiar</th>
          <th scope="col">Materiał</th>
          <th scope="col">Długość</th>
          <th scope="col">Długość Zrealizowana</th>
          <th scope="col">Ciężar</th>
          <th scope="col">Całkowity Ciężar</th>
          <th scope="col">Uwaga</th>
          <th scope="col">Data Operacji</th>
        </tr>
      </thead>
      <tbody>
        <?php
        require_once("v620.php");
        require_once("v630.php");
        ?>
        <?php
        while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
          if ($data['ilosc'] == 0 or $data['ilosc'] == '') {
            $szer = 0;
          } else {
            $szer = $data['AmountDone'] / $data['ilosc'] * 100;
          }

        ?>
          <tr>
            <td><?php echo $data['ProjectName']; ?></td>
            <td><?php echo $data['zespol']; ?></td>
            <td><?php echo $data['Name']; ?></td>
            <td>
              <div class="progress" style="height:25px;font-size: 16px;">
                <?php if ($szer <= 100) { ?>
                  <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo  $data['AmountDone']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $data['ilosc']; ?>'><?php echo $data['AmountDone']; ?></div>
                  <span class='progress-bar bg-white text-dark' style='width:
                  <?php if (100 - $szer < 0) {
                    echo 0;
                  } else {
                    echo 100 - $szer;
                  } ?>%;'><?php echo $data['ilosc']; ?> </span>
                <?php } else { ?>
                  <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo  $data['AmountDone']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $data['ilosc']; ?>'><?php echo $data['ilosc'] . "/" . $data['AmountDone']; ?></div>
                <?php }
                ?>
            </td>
            <td><?php echo $data['AmountNeeded']."/".$data['completev620']; ?></td>
            <td><?php echo $data['machine']; ?></td>
            <td><?php echo $data['Profil']; ?></td>
            <td><?php echo $data['Material']; ?></td>
            <td><?php echo $data['Dlugosc']; ?></td>
            <td><?php echo $data['SawLength']; ?></td>
            <td><?php echo $data['Ciezar']; ?></td>
            <td><?php echo $data['Calk_ciez']; ?></td>
            <td><?php echo $data['Uwaga']; ?></td>
            <td><?php if ($data['ModificationDate'] != "") {
                  echo $data['ModificationDate']->format('Y-m-d H:i:s');
                } ?>
            </td>
          </tr>
        <?php }  ?>

        <?php
        require_once("messer.php");

        while ($datamesser = sqlsrv_fetch_array($datasmesser, SQLSRV_FETCH_ASSOC)) {
          if ($datamesser['zapotrzebowanie'] == 0 or $datamesser['zapotrzebowanie'] == '') {
            $szermesser = 0;
          } else {
            $szermesser = $datamesser['Complet'] / $datamesser['zapotrzebowanie'] * 100;
          }
        ?>
          <tr>
            <td><?php echo $datamesser['Projekt']; ?></td>
            <td><?php echo $datamesser['Zespol']; ?></td>
            <td><?php echo $datamesser['PartName']; ?></td>

            <td>
              <div class="progress" style="height:25px;font-size: 16px;">


                <?php if ($szermesser <= 100) { ?>
                  <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow="<?php echo  $datamesser['Complet']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $datamesser['zapotrzebowanie']; ?>'><?php echo $datamesser['Complet']; ?></div>
                  <span class='progress-bar bg-white text-dark' style='width:
                  <?php if (100 - $szermesser < 0) {
                    echo 0;
                  } else {
                    echo 100 - $szermesser;
                  } ?>%;'><?php echo $datamesser["zapotrzebowanie"]; ?> </span>
                <?php } else { ?>
                  <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow='<?php echo  $datamesser['Complet']; ?>' aria-valuemin='0' aria-valuemax='<?php echo $datamesser["zapotrzebowanie"]; ?>'><?php echo $datamesser["zapotrzebowanie"] . "/" . $datamesser['Complet']; ?></div>
                <?php  }
                ?>
              </div>
            </td>
            <td><?php echo $datamesser['AmountNeeded']."/".$datamesser['completev620']; ?></td>
            <td><?php echo $datamesser['machine']; ?></td>
            <td><?php echo $datamesser['grubosc']; ?></td>
            <td><?php echo $datamesser['material']; ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align:right;">
              <?php if ($datamesser['DataWykonania'] != "") {
                echo $datamesser['DataWykonania']->format('Y-m-d H:i:s');
              } ?></td>
          </tr>
        <?php } ?>

        <?php
        require_once("othersql.php");

        while ($dataot = sqlsrv_fetch_array($dataother, SQLSRV_FETCH_ASSOC)) {
          if ($dataot['complet'] == 0 or $dataot['complet'] == '') {
            $szermesser = 0;
          } else {
            $szermesser = $dataot['complet'] / $dataot['ilosc'] * 100;
          }
        ?>
          <?php if ($szermesser >= 100) { ?>
            <tr>
            <?php } else { ?>
            <tr id="myRow" onclick="handleClick(this);">
            <?php } ?>
            <td id="project"><?php echo $dataot['ProjectName']; ?></i></td>
            <td id="zespol"><?php if ($dataot['status'] == 1) {
                              echo $dataot['aggregated_zespol'] . " <i class='bi bi-exclamation-triangle-fill text-danger'>";
                            } else {
                              echo $dataot['aggregated_zespol'];
                            } ?></td>
            <td id="detal"><?php echo $dataot['Name']; ?></td>
            <td>
              <div class="progress" style="height:25px;font-size: 16px;">

                <?php if ($szermesser <= 100) { ?>
                  <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow="<?php echo  $dataot['complet']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $$dataot['ilosc']; ?>'><?php echo $dataot['complet']; ?></div>
                  <span class='progress-bar bg-white text-dark' style='width:
                  <?php if (100 - $szermesser < 0) {
                    echo 0;
                  } else {
                    echo 100 - $szermesser;
                  } ?>%;'><?php echo $dataot['ilosc']; ?> </span>
                <?php } else { ?>
                  <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow='<?php echo  $dataot['complet']; ?>' aria-valuemin='0' aria-valuemax='<?php echo $dataot['ilosc']; ?>'><?php echo $dataot['ilosc'] . "/" . $dataot['complet']; ?></div>

              </div>
  </div>
<?php } ?>
</td>
<td></td>
<td><?php echo $dataot['machine']; ?></td>
<td><?php echo $dataot['profil']; ?></td>
<td><?php echo $dataot['material']; ?></td>
<td><?php echo $dataot['dlugosc']; ?></td>
<td><?php echo $dataot['dlugosc_zrea']; ?></td>
<td><?php echo $dataot['ciezar']; ?></td>
<td><?php echo $dataot['calk']; ?></td>
<td><?php echo $dataot['uwaga'] . "," . $dataot['wykonal']; ?></td>
<td><?php if ($dataot['data'] != "") {
            echo $dataot['data']->format('Y-m-d H:i:s');
          } ?></td>
</tr>
<?php } ?>
</tbody>
</table>


<div class="btn-toolbar position-fixed start-50 translate-middle-x" role="toolbar" aria-label="Toolbar with button groups" style="bottom:3%;">
  <div class="btn-group me-2" role="group" aria-label="First group">

    <?php if (!isUserParts()) { ?>
  <?php if (!isUserPartsKier()) { ?>
  <button type="button"  onclick="localStorage.removeItem('number1'); location.reload();" class="btn btn-warning btn-lg">Wyjdź</button>
  <?php } ?>
  <?php if (isUserPartsKier()) { ?>
    <form method="POST" action="statuschange.php">
  <button type="Submit" onclick="localStorage.removeItem('number1')" class="btn btn-warning btn-lg" name="role" value="role_parts">Wyjdź</button>
        </form>
        <button type="button"  onclick="localStorage.removeItem('number1'); location.reload();" class="btn btn-warning btn-lg">Przełącz</button>
<?php }} ?>
<?php if (isUserPartsKier() && isUserParts()) { ?>
  <form method="POST" action="statuschange.php">
  <button type="Submit" onclick="localStorage.removeItem('number1')" class="btn btn-warning btn-lg" name="role" value="role_parts">Przełącz</button>
        </form>
        <?php } ?>


  </div>
  <div class="btn-group me-2" role="group" aria-label="Second group">
    <?php if (!isUserParts()) { ?>
      <button type="button" onclick="sendSelectedRowsToPHP()" class="btn btn-warning btn-lg">Recznie</button>
      <button type="button" onclick="sendSelectedRowsToPHP1()" class="btn btn-warning btn-lg">Kooperacyjnie</button>
      <?php } ?>
      <?php if (isUserParts()) { ?>
      <button type="button" onclick="status()" class="btn btn-warning btn-lg">Status</button>
      <?php } ?>
  </div>
</div>




<div class="modal fade" id="mymodal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edycja projektu</h4>
      </div>
      <form method="POST" action="zapisze_dane.php" id="myForm">
        <div class="modal-body">
          Nazwa projektu: <label id="projectName" name="projectName"></label><br />
          <input type="hidden" name="project">
          Zespół: <label id="zespolName" name="zespolName"></label><br />
          Detal: <label id="detalName" name="detalName"></label><br />
          <input type="hidden" name="detal">
          <label id="numerName" name="numerName"></label>
          <input type="hidden" name="numer">
          <br />

          <?php if (!isUserParts()) { ?>
            <input class="form-control" type="number" inputmode="numeric" placeholder="Ilość" name="ilosc">
            <br />
            <input class="form-control" type="number" inputmode="numeric" placeholder="Długość" name="dlugosc">
            <br />

            <select class="form-control" name="maszyna" required>
              <option value="Recznie" selected>Recznie</option>
              <option value="Kooperacyjnie">Kooperacyjnie</option>
            </select>
          <?php } ?>
        </div>
        <div class="modal-footer">
          <?php
          if (isUserParts()) { ?>
            <button type="button" name="save" class="btn btn-default" value='usun' onclick="showConfirmation()">Kasuj Projekt</button>
          <?php }
          ?>
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
              $kiersql = "Select * from dbo.Persons where [user]=''";
              $stmt = sqlsrv_query($conn, $kiersql);
            ?> <select type="text" class="form-control" id="user-number" name="user-number" required>
                <?php
                while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                ?>
                  <option value="<?php echo $data['identyfikator'];  ?>"  data-imie-nazwisko="<?php echo $data['imie_nazwisko']; ?>"><?php echo $data['imie_nazwisko']; ?></option>

                <?php }
                ?>
              </select> <?php

                      } else if (!isUserPartsKier()) { ?>
              <input type="text" class="form-control" id="user-number" name="user-number">
            <?php } ?>
          </div>

          <div class="modal-footer">
            <?php
            if (isUserPartsKier()) { ?>
              <button id="submit-button" class="btn btn-default">Wyślij</button>
            <?php } else if (!isUserPartsKier()) { ?>
              <a href="..\index.php" class="btn btn-default">Strona główna</a>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php if (isUserPartsKier()) { ?>
<div id="myElement" class="bottom-banner1"></div>
<?php } ?>
</body>
<script>

$(document).ready(function() {
  $('#myTable').DataTable({
    "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
           '<"row"<"col-sm-12"tr>>' +
           '<"row"<"col-sm-5"i><"col-sm-7"p>>',
    "language": {
      "processing": "Przetwarzanie...",
      "search": "Szukaj: ",
      "lengthMenu": "Pokaż _MENU_ pozycji",
      "info": "Pozycje od _START_ do _END_ z _TOTAL_ łącznie",
      "infoEmpty": "Pozycji 0 z 0 dostępnych",
      "infoFiltered": "(filtrowanie spośród _MAX_ dostępnych pozycji)",
      "infoPostFix": "",
      "loadingRecords": "Wczytywanie...",
      "zeroRecords": "Nie znaleziono pasujących pozycji",
      "emptyTable": "Brak danych",
      "paginate": {
        "first": "Pierwsza",
        "previous": "&laquo;",
        "next": "&raquo;",
        "last": "Ostatnia"
      },
    },
    "initComplete": function () {
      // Dodaj klasy Bootstrap do przycisków i inputów
      $('#myTable_wrapper .dataTables_length select').addClass('form-select');
      $('#myTable_wrapper .dataTables_filter input').addClass('form-control');
      $('#myTable_wrapper .dataTables_paginate .pagination').addClass('pagination');
    }
  });
});




  function showConfirmation() {
    var form = document.getElementById("myForm");
    var result = confirm("Czy na pewno chcesz usunąć cały projekt?");
    if (result) {
      alert("Potwierdzono!");
      form.submit();
    } else {
      alert("Anulowano!");
    }
  }

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
      removeRowFromSelected(getColumnData(row, "project") + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
    } else {
      row.classList.add("table-warning");
      addRowToSelected(getColumnData(row, "project") + "," + getColumnData(row, "detal") + "," + localStorage.getItem('number1'));
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

  function doubleClickAction(row) {
    var projectName = row.querySelector('#project').innerHTML;
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
  var percent = parseInt(localStorage.getItem('czas')) || 0; // Jeśli 'czas' nie istnieje, użyj wartości 0

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
      nazwa= localStorage.getItem('nazwa');
      if (stored) {
        // Numer został już poprawnie sprawdzony, nie wyświetlamy okna dialogowego
        console.log('Numer został już sprawdzony: ' + stored);
        toastr.success('Weryfikacja przebiegła pomyślnie!!!<br/> Witaj '+nazwa);
        document.getElementById('myElement').innerHTML = "Pracujesz w kontekście <br>"+nazwa;
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
                localStorage.setItem('nazwa',czesci[1]);
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