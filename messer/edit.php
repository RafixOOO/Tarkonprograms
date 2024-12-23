<?php
require_once('../auth.php');

require_once("dbconnect.php");
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $comment = "";
    $id = "";
    $id = $_GET["id"];
    $osoba = $_GET["numbermesser"];
    $sql = "SELECT [Comment] FROM [SNDBASE_PROD].[dbo].[Program] where [ArchivePacketID]=$id";
    $res = sqlsrv_query($conn, $sql);
    $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
    $comment = "$row[Comment]";
} else {
    $aktualna_data_i_czas = date("Y-m-d H:i:s");
    $raz=1;
    if($_POST['comment']=="wykonano"){
        $sql1 = "UPDATE [SNDBASE_PROD].[dbo].[Program]
                    SET [Comment]='$_POST[numbermesser],$aktualna_data_i_czas'
               where [ArchivePacketID]=$_POST[id]";

    }else{
        $dane=$_POST['comment'].' '.$_POST['numbermesser'];
        $sql1 = "UPDATE [SNDBASE_PROD].[dbo].[Program]
                    SET [Comment]='$dane,$aktualna_data_i_czas'
               where [ArchivePacketID]=$_POST[id]";
        if($raz=1){
        require_once 'mail.php';
        $raz=0;
    }
    }

    
    sqlsrv_query($conn, $sql1);
    $tsql1 = "SELECT
        [ArchivePacketID]
        ,[Comment]
        FROM [SNDBASE_PROD].[dbo].[Program]
        ORDER BY [Comment]";

    $res1 = sqlsrv_query($conn, $tsql1);
    $i = "0A";
    while ($row1 = sqlsrv_fetch_array($res1, SQLSRV_FETCH_ASSOC)) {

        $sql1 = "UPDATE [SNDBASE_PROD].[dbo].[Program]
             SET [Comment]=Concat('$i,',PARSENAME(REPLACE([Comment], ',', '.'), 1))
             where [ArchivePacketID]=$row1[ArchivePacketID] and [Comment] LIKE '[0-9]%'";
        $i++;
        sqlsrv_query($conn, $sql1);
    }

    logUserActivity($_POST['numbermesser'],'update w aplikacji messer o id: '.$_POST['id']);
    sqlsrv_close($conn);
    header("location: main.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'globalhead.php'; ?>
    <style>
         .verticalrotate {
      position: fixed;
      bottom: 50%;
      left: 84.5%;
      width: 30%;
      transform: rotate(-90deg);
    }
    </style>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
    <div class="container">
    <?php if (!isLoggedIn()) { ?>
      <div class="progress verticalrotate">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="time"></div>
      </div>
    <?php } ?>
        <br />
        <h2 class="text-uppercase">Edycja</h2>
        <form method="Post">

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Czy wykonano? / Powód nie wykonania</label>
                <div class="col-sm-6" for="input-lg">
                    <label>Pracownik: <?php echo $osoba; ?></label>
                    <select class="form-control" name="comment">
                        <option value="wykonano" selected>Wykonano</option>
                        <option value="nie znaleziono arkusza">nie znaleziono arkusza</option>
                        <option value="zla jakosc otworow">zła jakośc otworów </option>
                        <option value="zla jakosc faz">zła jakość faz </option>
                        <option value="brak arkusza">Brak arkusza</option>
                        <option value="inne">inne</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button class="btn btn-outline-primary btn-lg" type="Submit">Zapisz</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary btn-lg" href="main.php" role="button">Anuluj</a>
                </div>
            </div>
            <input type="hidden" id="darkModeButton" />

    </div>
    <input type="hidden" name="id" value="<?php echo $id ?>" />
    <input type="hidden" name="numbermesser" value="<?php echo $osoba ?>" />
    <input type="hidden" name="comment1" value="<?php echo $comment ?>" />
    </form>
    </div>
</body>
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
          window.location.href = 'main.php';
        }
      }

      changeColor(); // Wywołaj funkcję changeColor() po załadowaniu strony
    }
</script>
</html>