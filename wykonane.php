<!DOCTYPE html>
<html lang="en">
<?php
require_once('auth.php');
requireLogin();

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
    
    <title>Programy wykonane</title>
    <?php include 'globalhead.php'; ?>

</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark">
    <div class="container">
        <br />
        <?php include 'globalnav.php'; ?>
        <br />
        <h2 class="text-uppercase">Programy wykonane</h2><br>

        <?php require_once('dbconnect.php');
        $sql = "SELECT [ProgramName]
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
        ORDER BY [Comment]";
        $datas = sqlsrv_query($conn, $sql);

        ?>
        <table class="table table-hover" id="mytable">
            <thead>
                <th>Imię i Nazwisko</th>
                <th>Nazwa programu</th>
                <th>Nazwa arkusza</th>
                <th>Nazwa maszyny</th>
                <th>Materiał</th>
                <th>Grubość</th>
                <th>Długość arkusza</th>
                <th>Szerokość arkusza</th>
                <th>Czas spalania</th>
            </thead>
            <tbody class="row_position">
                <?php

                while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) { ?>

                    <?php if ($data["Comment"] == "SYLWESTER WOZNIAK" | $data["Comment"] == "MARCIN MICHAS" | $data["Comment"] == "LUKASZ PASEK" | $data["Comment"] == "ARTUR BEDNARZ" | $data["Comment"] == "DARIUSZ MALEK") { ?>
                        <tr class="table-success" id="<?php echo $data['ArchivePacketID'] ?>">
                            <td>
                                <?php echo "$data[Comment] "; ?>
                            </td>

                            <td>
                                <?php echo "$data[ProgramName]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[SheetName]"; ?>
                            </td>
                            <td>
                                <?php echo "$data[MachineName]"; ?>
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
                        <?php }
                }
                sqlsrv_close($conn);
                ?>


                </tr>

            </tbody>
        </table>
    </div>

</body>
<script>
    let mybutton = document.getElementById("btn-back-to-top");

window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  if (
    document.body.scrollTop > 20 ||
    document.documentElement.scrollTop > 20
  ) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

mybutton.addEventListener("click", backToTop);

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script>
</script>

</html>