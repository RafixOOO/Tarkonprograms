<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'globalhead.php'; ?>
    <style>
    #exit-button {
        position: fixed;
        bottom: 10px; /* odległość od dolnej krawędzi ekranu */
        left: 10px; /* odległość od lewej krawędzi ekranu */
    }

    .tile-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.tile {
  width: calc(33.33% - 10px); /* Ustaw szerokość kafelka dla trzech kolumn (odejmujemy mniejszy margines) */
  margin: 5px;
  padding: 10px;
  box-sizing: border-box;
  border: 1px solid #ccc;
  cursor: pointer;
  transition: background-color 0.3s;
  text-align:center;
}

.tile:hover {
  background-color: #f0f0f0;
}

/* Dla ostatnich dwóch kafelków w rzędzie */
.tile:nth-last-child(-n+2) {
  width: calc(50% - 10px); /* Ustaw szerokość na 50% dla ostatnich dwóch elementów */
  margin-right: 0; /* Usuń margines z prawej strony dla ostatnich dwóch elementów */
}
.verticalrotate {
      position: fixed;
      bottom: 50%;
      left: 84.5%;
      width: 30%;
      transform: rotate(-90deg);
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

<?php include 'globalnav.php'; ?>
    <div class="container-xxl">
    <?php if (!isLoggedIn()) { ?>
      <div class="progress verticalrotate">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
      </div>
    <?php } ?>
    <div>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped table-bordered" id="mytable" style="font-size: calc(9px + 0.390625vw)">
                <thead>
                    <th>#</th>
                    <th>Program name</th>
                    <th>Sheet name</th>
                    <th>Material</th>
                    <th>Thickness</th>
                    <th>sheet length</th>
                    <th>width length</th>
                    <th>Burning time</th>
                    <th>Amount</th>
                    <?php if(!isLoggedIn()) { ?>
                    <th>Options</th>
                    <?php } ?>


                </thead>
                <tbody class="row_position">
                    <?php
                    $time="";
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
                                        echo "
                                        <details>
                                <summary>Rozwiń</summary>
                                <label>" . $data["part"] . "</label>
                                </details>
                            ";
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
                                <?php if(!isLoggedIn()) { ?>
                                <td>
                                    
                                <a class='btn btn-primary btn-sm' href='#' onclick="addNumberMesserToURL('<?php echo $data['ArchivePacketID']; ?>')">Zarządzaj</a>
                                </td>
                                <?php } ?>
                                </tr>
                
                    <?php } } ?>                
                </tbody>

            </table>
            <?php if (!isUserMesser()) { ?>
              <button type="button" id="exit-button" onclick="localStorage.removeItem('numbermesser'); location.reload();" class="btn btn-warning btn-lg">Wyjdź</button>
            <?php } ?>
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
</body>
<script src="../static/jquery.min.js"></script>
<script src="../static/jquery-ui.min.js"></script>
<script src="../static/toastr.min.js"></script>
<?php if (!isLoggedIn()) { ?>
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
        if(!localStorage.getItem('numbermesser')){
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


<?php

if (isUserMesser()) {


    echo "<script type='text/javascript'>

    function validateInput(event) {
        const input = event.target;
        const value = input.value;
        
        if (value.includes(',') || value.includes('.')) {
          input.value = value.replace(/[,\.]/g, '');
        }
      }

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
</script>";

}
?>
</html>