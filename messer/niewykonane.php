<!DOCTYPE html>
<html lang="en">
<?php

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

    <?php include 'globalhead.php'; ?>

</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark">
<?php include 'globalnav.php'; ?>
    <div class="container">
    <div>
             <ul style="float:left;" class="nav nav-tabs" id="myTab" role="tablist">
                 <li class="nav-item">
                     <a class="nav-link active" id="home-tab" data-toggle="tab" href="main.php" role="tab" aria-selected="true">Aktualne</a>
                 </li>
                 <li class="nav-item ">
                     <a class="nav-link active" id="profile-tab" data-toggle="tab" href="wykonane.php" role="tab" aria-controls="profile" aria-selected="true">Wykonane
                     </a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link active" id="contact-tab" data-toggle="tab" href="niewykonane.php" role="tab" aria-controls="Nie wykonane" aria-selected="true">Nie wykonane</a>
                 </li>
             </ul>
             <br /><br />
        
        <h3 class="text-uppercase">Programy nie wykonane</h3><br>

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
                <th>Powód</th>
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

                while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) { ?>

                    <?php if ($data["Comment"] == "nie znaleziono arkusza" | $data["Comment"] == "zla jakosc otworow" | $data["Comment"] == "zla jakosc faz" | $data["Comment"] == "inne") { ?>
                        <tr class="table-danger" id="<?php echo $data['ArchivePacketID'] ?>">
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
                            <td>
                                <a class='btn btn-primary btn-sm'
                                    href='edit.php?id=<?php echo $data["ArchivePacketID"]; ?>'>Zarządzaj</a>
                            </td>
                        <?php }
                }
                sqlsrv_close($conn);
                ?>


                </tr>

            </tbody>
        </table>
    </div>
    </div>
    </div>
</div>
</body>

</script>

</html>