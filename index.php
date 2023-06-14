<!DOCTYPE html>
<html lang="en">

<?php
require_once('auth.php');
requireLogin();

function isUserAdmin()
{
    return isset($_SESSION['username']) && $_SESSION['role'] === 'admin';
}

?>

<head>
    <title>Programy aktualne</title>
    <?php include 'globalhead.php'; ?>
</head>
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
,PARSENAME(REPLACE([Comment], ',', '.'), 1) as part
,CONVERT (CHAR(8),DATEADD(second, [CuttingTime],0) ,108) as czaspalenia
FROM [SNDBASE_PROD].[dbo].[Program]
ORDER BY [Comment]";
$datas = sqlsrv_query($conn, $sql);

$sql2 = "SELECT 
            Max([Comment]) as zupa

                FROM [SNDBASE_PROD].[dbo].[Program]
                 where [Comment] LIKE '[0-9]%'";
$res1 = sqlsrv_query($conn, $sql2);
$max = "";
while ($row1 = sqlsrv_fetch_array($res1, SQLSRV_FETCH_ASSOC)) {
    $max = $row1["zupa"];
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

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark">
    <div class="container">
        <br />
        <?php include 'globalnav.php'; ?>
        <br />
        <h2 class="text-uppercase">Programy Aktualne</h2><br>
        <div class="table-responsive">
            <table class="table table-hover" id="mytable">
                <thead>
                    <th>#</th>
                    <th>Nazwa programu</th>
                    <th>Nazwa arkusza</th>
                    <th>Nazwa maszyny</th>
                    <th>Materiał</th>
                    <th>Grubość</th>
                    <th>Długość arkusza</th>
                    <th>Szerokość arkusza</th>
                    <th>Czas spalania</th>
                    <th>Opcje</th>


                </thead>
                <tbody class="row_position">
                    <?php
                    $i = 1;
                    while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {

                        if (empty($data["Comment"])) {
                            $max++;
                            $sql = "UPDATE [SNDBASE_PROD].[dbo].[Program]
                                    SET [Comment]='$max'
                                    WHERE [ArchivePacketID]=$data[ArchivePacketID]";
                            sqlsrv_query($conn, $sql);
                        }

                        if (czyCiągZawieraLiczbyPHP($data["Comment"]) == true) {

                            ?>

                            <tr id="<?php echo $data['ArchivePacketID'] ?>">
                                <td>
                                    <?php
                                    if (isUserAdmin()) {
                                        echo "
                                <details><summary>Rozwiń</summary>
                                <form id='myForm' action='update.php' method='POST'>
                                <input type='hidden' name='id' value='$data[ArchivePacketID]'>
                                <input type='hidden' name='lop' value='$data[Comment]'>
                                <input type='text' name='myField' id='myField' oninput='validateInput(event)' Placeholder='$data[part]'>
                                </form>

                                </details>
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

                                <td>
                                    <a class='btn btn-primary btn-sm'
                                        href='edit.php?id=<?php echo $data["ArchivePacketID"]; ?>'>Zarządzaj</a>
                                </td>
                            <?php }
                    } ?>



                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


<script>
    $('#colorbox').mousemove(function (event) {

        clearTimeout($(this).data('timer'));

        $(this).data('timer', setTimeout(function () {
            window.location.reload();
        }, 5000));
    });
</script>
<?php
if (isUserAdmin()) {


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
                toastr.success('Zmiany zostały zapisane');
            }
        })
    }
</script>";

}
?>

</html>