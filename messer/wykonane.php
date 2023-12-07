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

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php include 'globalnav.php'; ?>
    <div class="container-xxl">

    <div>

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
         <div class="table-responsive">
            <table class="table table-sm table-hover table-striped table-bordered" id="mytable" style="font-size: calc(9px + 0.390625vw)">
            <thead>
                <th>Person</th>
                <th>Program name</th>
                    <th>Sheet name</th>
                    <th>Material</th>
                    <th>Thickness</th>
                    <th>sheet length</th>
                    <th>width length</th>
                    <th>Burning time</th>
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
                            </tr>
                        <?php }else if ($data["Comment"] == "nie znaleziono arkusza" | $data["Comment"] == "zla jakosc otworow" | $data["Comment"] == "zla jakosc faz" | $data["Comment"] == "inne") { ?>
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
                                </tr>
                            <?php } ?>
                            
                <?php }
                ?>


                

            </tbody>
        </table>
    </div>
    </div>
    </div>
</div>
</body>
</script>

</html>