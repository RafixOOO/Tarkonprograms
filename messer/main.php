<!DOCTYPE html>
<html lang="en">
<?php require_once '../auth.php'; ?>

<head>
  <?php include 'globalhead.php'; ?>
  <style>
    .btn-warning {
      position: fixed;
      bottom: 10px;
      /* odległość od dolnej krawędzi ekranu */
      right: 10px;
      /* odległość od lewej krawędzi ekranu */
    }

    .tile-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }

    .tile {
      width: calc(33.33% - 10px);
      /* Ustaw szerokość kafelka dla trzech kolumn (odejmujemy mniejszy margines) */
      margin: 5px;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      cursor: pointer;
      transition: background-color 0.3s;
      text-align: center;
    }

    .tile:hover {
      background-color: #f0f0f0;
    }

    /* Dla ostatnich dwóch kafelków w rzędzie */
    .tile:nth-last-child(-n+2) {
      width: calc(50% - 10px);
      /* Ustaw szerokość na 50% dla ostatnich dwóch elementów */
      margin-right: 0;
      /* Usuń margines z prawej strony dla ostatnich dwóch elementów */
    }

    .verticalrotate {
      position: fixed;
      bottom: 50%;
      left: 84.5%;
      width: 30%;
      transform: rotate(-90deg);
    }

    #chatContainer {
      bottom: 10px;
      right: 10px;
      max-width: 400px;
    }

    .chat {
      border: 2px solid #dedede;
      background-color: #f1f1f1;
      border-radius: 5px;
      padding: 5px;
      margin: 5px 0;
    }

    .darker {
      border-color: #ccc;
      background-color: #ddd;
      text-align: right;
    }

    .chat::after {
      content: "";
      clear: both;
      display: table;
    }

    .time-right {
      float: right;
      color: #aaa;
    }

    .time-left {
      float: left;
      color: #999;
    }

    #chatInputContainer {
      left: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 5px 10px;
    }

    #chatInput {
      width: calc(100% - 70px);
      margin-right: 10px;
      padding: 5px;
      border: 1px solid #ddd;
      border-radius: 3px;
      outline: none;
    }

    #sendButton {
      width: 60px;
      padding: 5px;
      margin: 0;
      border: 1px solid #ddd;
      border-radius: 3px;
      background-color: #4CAF50;
      color: white;
      cursor: pointer;
    }

    .highlight {
      background-color: yellow;
      }
  </style>
</head>
<?php require_once('dbconnect.php');
$sql = "SELECT p.[ProgramName]
,p.[ArchivePacketID]
,p.[SheetName]
,p.[MachineName]
,p.[Material]
,p.[Thickness]
,p.[SheetLength]
,p.[SheetWidth]
,p.[ActualStartTime]
,p.[ActualEndTime]
,p.[ActualState]
,p.[ActualTimeSyncNeeded]
,p.[Comment]
,Sum(q.[QtyInProcess]) as liczba
,PARSENAME(REPLACE(p.[Comment], ',', '.'), 1) as part
,CONVERT (CHAR(8),DATEADD(second, p.[CuttingTime],0) ,108) as czaspalenia
FROM [SNDBASE_PROD].[dbo].[Program] p
Left join [SNDBASE_PROD].[dbo].[PIP] q ON p.ProgramName=q.ProgramName
where ISNUMERIC(LEFT(p.[Comment], 1)) = 1 or p.[Comment]=''
group by p.[ProgramName]
,p.[ArchivePacketID]
,p.[SheetName]
,p.[MachineName]
,p.[Material]
,p.[Thickness]
,p.[SheetLength]
,p.[SheetWidth]
,p.[ActualStartTime]
,p.[ActualEndTime]
,p.[ActualState]
,p.[ActualTimeSyncNeeded]
,p.[Comment],p.[CuttingTime] ORDER BY [Comment]";
$datas = sqlsrv_query($conn, $sql);

$sql2 = "SELECT 
            Max([Comment]) as zupa

                FROM [SNDBASE_PROD].[dbo].[Program]
                 where [Comment] LIKE '[0-9]%'";
$res1 = sqlsrv_query($conn, $sql2);
$max = "";
while ($row1 = sqlsrv_fetch_array($res1, SQLSRV_FETCH_ASSOC)) {
  $max = substr($row1["zupa"], 0, 2);
}
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

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<div class="container-fluid" style="width:80%;margin-left:auto;margin-right:auto;">
<ul class="nav nav-pills nav-primary" style="margin-left:auto;margin-right:auto;">
                      <li class="nav-item">
                        <a class="nav-link active" href="main.php">Programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="wykonane.php" onclick="localStorage.removeItem('numbermesser')">Zakończone programy</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="magazyn.php" onclick="localStorage.removeItem('numbermesser')">Magazyn</a>
                      </li>
                    </ul>
    <?php if (!isLoggedIn()) { ?>
      <div class="progress verticalrotate">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
      </div>
    <?php } ?>
    <div>
      <div class="table-responsive">
      <?php if (isLoggedIn()) { ?>
    <input type="text" id="search" placeholder="Wyszukaj w tabeli..." oninput="highlightTableText()" style="float:right;">
    <?php } ?>
        <table class="table table-sm table-hover table-striped table-bordered" id="mytable" style="font-size: calc(9px + 0.390625vw)">
          <thead>
            <th>#</th>
            <th>Nazwa programu</th>
            <th>Nazwa Arkusza</th>
            <th>Materiał</th>
            <th>Grubość</th>
            <th>Długość arkusza</th>
            <th>Szerokość arkusza</th>
            <th>Czas</th>
            <th>Liczba</th>
            <?php if (!isLoggedIn()) { ?>
              <th>Options</th>
            <?php } ?>


          </thead>
          <tbody class="row_position">
            <?php
            $time = "";
            $i = 1;
            while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {

              if (empty($data["Comment"])) {
                $max++;
                $sql = "UPDATE [SNDBASE_PROD].[dbo].[Program]
                                    SET [Comment]='$max,'
                                    WHERE [ArchivePacketID]=$data[ArchivePacketID]";
                sqlsrv_query($conn, $sql);
              }

              if (czyCiągZawieraLiczbyPHP($data["Comment"]) == true) {

            ?>

                <tr id="<?php echo $data['ArchivePacketID'] ?>" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  <td>
                    <?php
                    if (isUserMesser()) {
                      echo "
                                        <details>
                                <summary>Rozwiń</summary><form id='myForm' action='update.php' method='POST'>
                                <input type='hidden' name='id' value='$data[ArchivePacketID]'>
                                <input type='hidden' name='lop' value='$data[Comment]'>
                                <input type='text' name='myField' id='myField' oninput='validateInput(event)' Placeholder='$data[part]'>
                                </form></details>
                                
                            ";
                    } else if (!empty($data["part"])) {
                      echo "".$data["part"]."";
                    }


                    ?>
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
                    <?php echo "$data[liczba]"; ?>
                  </td>
                  <?php if (!isLoggedIn()) { ?>
                    <td>

                      <a class='btn btn-primary btn-sm' href='#' onclick="addNumberMesserToURL('<?php echo $data['ArchivePacketID']; ?>')">Zarządzaj</a>
                    </td>
                  <?php } ?>
                </tr>

            <?php }
            } ?>
          </tbody>

        </table>
        <br />
        <?php if (!isLoggedIn()) { ?>
          <button type="button" id="exit-button" onclick="localStorage.removeItem('numbermesser'); location.reload();" class="btn btn-warning btn-lg">Wyjdź</button>
          <button style="right: 100px" type="button" id="toggleChatButton" class="btn btn-warning btn-lg" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Chat</button>
        <?php } else if (isUserMesser() | !isLoggedIn()) { ?>
          <button type="button" id="toggleChatButton" class="btn btn-warning btn-lg" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Chat</button>
        <?php } ?>
      </div>
      <div class="offcanvas offcanvas-start" data-bs-scroll="false" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="chatInputContainer"><input type="text" id="message-input" placeholder="Type your message..." >
          <button id="sendButton" onclick="sendMessage()">Send</button></h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body" id="chatContainer">
        </div>
        <div class="offcanvas-footer" >
          
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
  <div class="modal" id="user-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Identyfikacja użytkownika</h5>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <?php
            $kiersql = "Select * from dbo.Persons where [user]='' and [prac_messer]=1";
            $stmt = sqlsrv_query($conn, $kiersql);
            ?>
            <div class="tile-container">
              <?php
              while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
              ?>
                <div class="tile" data-imie-nazwisko="<?php echo $data['imie_nazwisko']; ?>">
                  <?php echo $data['imie_nazwisko']; ?>
                </div>
              <?php
              }
              ?>
            </div>

          </div>

          <div class="modal-footer">
            <a href="../login.php" class="btn btn-default">Zaloguj się</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if(!isLoggedIn()) { ?>
  <link rel="stylesheet" href="../assets/css/plugins.min.css"/>
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css"/>
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

<script src="../static/jquery-ui.min.js"></script>
<script src="../static/toastr.min.js"></script>
<?php if (!isLoggedIn()) { ?>
  <script>
    function sendMessage() {
      var message = $('#message-input').val();

      if (message.trim() !== '') {
        $.ajax({
          url: 'send_message.php',
          method: 'POST',
          data: {
            message: message,
            user: localStorage.getItem('numbermesser')
          },
          success: function() {
            // Wyczyszczenie pola wprowadzania tekstu po wysłaniu wiadomości
            $('#message-input').val('');
            // Odświeżenie widoku wiadomości
            loadMessages();
          }
        });
      }
    }

    var previousMessageCount = 0; // Dodana definicja zmiennej previousMessageCount
    var currentMessageCount = 0;
    var isFirstToast = true;

    function loadMessages() {
      $.ajax({
        url: 'get_messages.php',
        method: 'GET',
        success: function(data) {
          $('#chatContainer').html(''); // Czyść zawartość chatContainer przed dodaniem nowych wiadomości


          try {
            var messages = JSON.parse(data);

            if (Array.isArray(messages)) {
              currentMessageCount = messages.length;

              // Iteruj przez wiadomości i dodawaj je do chatContainer
              messages.forEach(function(message) {
                var sender = message.osoba; // Zmieniłem 'osoba' na 'sender' dla zgodności z poprzednim kodem
                var content = message.massage;
                var time = new Date(message.time.date);
                if (localStorage.getItem('numbermesser') == sender) {
                  var isDarker = true;
                } else {
                  var isDarker = false;
                }
                // Tutaj możesz dodać logikę, aby określić, czy wiadomość ma być ciemniejsza
                var formattedDateTime = time.getFullYear() + '-' +
                  ('0' + (time.getMonth() + 1)).slice(-2) + '-' +
                  ('0' + time.getDate()).slice(-2) + ' ' +
                  ('0' + time.getHours()).slice(-2) + ':' +
                  ('0' + time.getMinutes()).slice(-2);

                addMessageToChat(sender, content, formattedDateTime, isDarker);
              });
            }
          } catch (error) {
            console.error('Błąd parsowania danych JSON:', error);
          }


          if (currentMessageCount > previousMessageCount) {
            if (!isFirstToast) {
              // Wyślij powiadomienie do systemu Windows
              if ('Notification' in window) {
                Notification.requestPermission().then(function (permission) {
                  if (permission === 'granted') {
                    var notification = new Notification('Messer', {
                      body: 'Masz nową wiadomość na Czacie'
                    });

                    // Dodaj dźwięk (opcjonalne)
                    var audio = new Audio('muzykamassage.mp3');
                    audio.play();
                  }
                });
              }
            }

            isFirstToast = false;
            previousMessageCount = currentMessageCount;
          }
        }
      });
    }

    // Odświeżenie widoku wiadomości co pewien czas
    setInterval(loadMessages, 2000);
  </script>
  <script>
    if (localStorage.getItem('numbermesser') !== null) {
      var colorButton = document.getElementById('time');
      var percent = 0;

      function changeColor() {
        percent += 0.5;
        colorButton.style.width = `${percent}%`;

        if (percent < 100) {
          setTimeout(changeColor, 200); // Powtórz co 1 sekundę (1000 milisekund)
          localStorage.setItem('czas', percent);
        } else {
          localStorage.removeItem('numbermesser');
          location.reload();
        }
      }

      changeColor(); // Wywołaj funkcję changeColor() po załadowaniu strony
    }


    function addNumberMesserToURL(archivePacketID) {
      var numberMesser = localStorage.getItem('numbermesser');

      // Sprawdź, czy numberMesser jest zdefiniowane
      if (numberMesser) {
        // Utwórz dynamiczny URL z dodanym parametrem numbermesser
        var url = 'edit.php?id=' + archivePacketID + '&numbermesser=' + encodeURIComponent(numberMesser);

        // Przejdź do nowego URL
        window.location.href = url;
      } else {
        // W przypadku braku numbermesser, po prostu przejdź do standardowego URL
        window.location.href = 'edit.php?id=' + archivePacketID;
      }
    }
    $(document).ready(function() {
      $('#user-modal').modal({
        backdrop: 'static',
        keyboard: false
      });
      if (!localStorage.getItem('numbermesser')) {
        $('#user-modal').modal('show');
      }

      $('#user-number').focus();


      $('#user-modal').on('shown.bs.modal', function() {
        selectInput();
      });
      $('.tile').on('click', function(e) {
        // Funkcja wywołana przy kliknięciu na kafelek pracownika
        e.preventDefault();

        var userNumber = $(this).data('imie-nazwisko');
        localStorage.setItem('numbermesser', userNumber);
        $('#user-modal').modal('hide');
        console.log(localStorage.getItem('numbermesser'));
        location.reload();
      });
    });
  </script>
<?php } ?>

<script>
  function addMessageToChat(sender, content, time, isDarker) {

    // Utwórz nowy element wiadomości
    var newMessage = $("<div>").addClass("chat").addClass(isDarker ? "darker" : "").addClass("show");

    // Dodaj treść wiadomości
    newMessage.append("<p><b>" + sender + "</b></p>");
    newMessage.append("<p>" + content + "</p>");
    newMessage.append("<span class='" + (isDarker ? "time-left" : "time-right") + "'>" + time + "</span>");
    newMessage.append("</div>");

    // Dodaj nową wiadomość do kontenera
    $("#chatContainer").append(newMessage);
  }

function highlightTableText() {
  const searchText = document.getElementById('search').value.toLowerCase();
  const table = document.getElementById('mytable');
  const rows = table.getElementsByTagName('tr');

  // Resetowanie podświetleń
  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName('td');
    for (let j = 0; j < cells.length; j++) {
      cells[j].innerHTML = cells[j].innerText;
    }
  }

  // Jeśli pole wyszukiwania jest puste, zakończ funkcję
  if (!searchText) return;

  // Podświetlanie znalezionego tekstu
  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName('td');
    for (let j = 0; j < cells.length; j++) {
      const cellText = cells[j].innerText.toLowerCase();
      if (cellText.includes(searchText)) {
        const regex = new RegExp(searchText, 'gi');
        cells[j].innerHTML = cells[j].innerText.replace(regex, match => `<span class="highlight">${match}</span>`);
      }
    }
  }
}

</script>
<?php

if (isUserMesser()) {

  echo "
  <script>

  function sendMessage() {
    var message = $('#message-input').val();
    
    if (message.trim() !== '') {
        $.ajax({
            url: 'send_message.php',
            method: 'POST',
            data: { message: message,
              user:" . json_encode($_SESSION['imie_nazwisko'], JSON_HEX_QUOT | JSON_HEX_TAG) . "},
            success: function () {
                // Wyczyszczenie pola wprowadzania tekstu po wysłaniu wiadomości
                $('#message-input').val('');
                // Odświeżenie widoku wiadomości
                loadMessages();
            }
        });
    }
}

var previousMessageCount = 0; // Dodana definicja zmiennej previousMessageCount
var currentMessageCount = 0;
var isFirstToast = true;

function loadMessages() {
  $.ajax({
    url: 'get_messages.php',
    method: 'GET',
    success: function(data) {
      $('#chatContainer').html(''); // Czyść zawartość chatContainer przed dodaniem nowych wiadomości


      try {
        var messages = JSON.parse(data);

        if (Array.isArray(messages)) {
          currentMessageCount = messages.length;

          // Iteruj przez wiadomości i dodawaj je do chatContainer
          messages.forEach(function(message) {
            var sender = message.osoba; // Zmieniłem 'osoba' na 'sender' dla zgodności z poprzednim kodem
            var content = message.massage;
            var time = new Date(message.time.date);
            if(" . json_encode($_SESSION['imie_nazwisko'], JSON_HEX_QUOT | JSON_HEX_TAG) . "==sender){
              var isDarker = true;
            }else{
              var isDarker = false;
            }
             // Tutaj możesz dodać logikę, aby określić, czy wiadomość ma być ciemniejsza
            var formattedDateTime = time.getFullYear() + '-' +
              ('0' + (time.getMonth() + 1)).slice(-2) + '-' +
              ('0' + time.getDate()).slice(-2) + ' ' +
              ('0' + time.getHours()).slice(-2) + ':' +
              ('0' + time.getMinutes()).slice(-2);

            addMessageToChat(sender, content, formattedDateTime, isDarker);
          });
        }
      } catch (error) {
        console.error('Błąd parsowania danych JSON:', error);
      }

      if (currentMessageCount > previousMessageCount) {
    if (!isFirstToast) {
        // Wyślij powiadomienie do systemu Windows
        if ('Notification' in window) {
            Notification.requestPermission().then(function (permission) {
                if (permission === 'granted') {
                    var notification = new Notification('Messer', {
                        body: 'Masz nową wiadomość na Czacie'
                    });

                    // Dodaj dźwięk (opcjonalne)
                    var audio = new Audio('muzykamassage.mp3');
                    audio.play();
                }
            });
        }
    }

    isFirstToast = false;
    previousMessageCount = currentMessageCount;
}
    }
  });
}

// Odświeżenie widoku wiadomości co pewien czas
setInterval(loadMessages, 2000);


  </script>";
  echo "<script type='text/javascript'>

    function validateInput(event) {
        const input = event.target;
        const value = input.value;
        
        if (value.includes(',') || value.includes('.')) {
          input.value = value.replace(/[,\.]/g, '');
        }
      }
try {
    document.getElementById('myField').addEventListener('keydown', function(event) {
        

        if (event.keyCode === 13) { 
          event.preventDefault();
          document.getElementById('myForm').submit();
        }
      });

    $('.row_position').sortable({
        delay: 150,
        stop: function () {
            var selectedData = new Array();
            $('.row_position>tr').each(function () {
                selectedData.push($(this).attr('id'));
            });
            updateOrder(selectedData);
        }
    });

    function updateOrder(aData) {
        $.ajax({
            url: 'sort.php',
            type: 'POST',
            data: {
                allData: aData
            },
            success: function (data) {
                location.reload();
            }
        })
    }
}catch(error){
          console.error('Błąd: ', error);

}
</script>";
}
?>

</html>