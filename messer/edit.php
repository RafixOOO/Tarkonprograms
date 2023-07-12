<?php
require_once('../auth.php');

require_once("dbconnect.php");
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $comment = "";
    $id = "";
    $id = $_GET["id"];
    $sql = "SELECT [Comment] FROM [SNDBASE_PROD].[dbo].[Program] where [ArchivePacketID]=$id";
    $res = sqlsrv_query($conn, $sql);
    $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
    $comment = "$row[Comment]";
} else {

    $sql1 = "UPDATE [SNDBASE_PROD].[dbo].[Program]
                    SET [Comment]='$_POST[comment]'
               where [ArchivePacketID]=$_POST[id]";
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
    sqlsrv_close($conn);
    header("location: main.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'globalhead.php'; ?>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <div class="container">
        <br />
        <h2 class="text-uppercase">Edycja</h2>
        <form method="Post">

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Kto wykonał? / Powód nie wykonania</label>
                <div class="col-sm-6">
                    <select class="form-control" name="comment">
                        <option value="<?php echo $comment; ?>" selected>
                        </option>
                        <option value="SYLWESTER WOZNIAK">Wykonano - SYLWESTER WOZNIAK</option>
                        <option value="MARCIN MICHAS">Wykonano - MARCIN MICHAS</option>
                        <option value="LUKASZ PASEK">Wykonano - LUKASZ PASEK</option>
                        <option value="ARTUR BEDNARZ">Wykonano - ARTUR BEDNARZ</option>
                        <option value="DARIUSZ MALEK">Wykonano - DARIUSZ MALEK</option>
                        <option value="nie znaleziono arkusza">Nie Wykonano - nie znaleziono arkusza</option>
                        <option value="zla jakosc otworow">Nie Wykonano - zła jakośc otworów </option>
                        <option value="zla jakosc faz">Nie Wykonano - zła jakość faz </option>
                        <option value="inne">Nie Wykonano - inne </option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button class="btn btn-outline-primary" type="Submit">Zapisz</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="main.php" role="button">Anuluj</a>
                </div>
            </div>

    </div>
    <input type="hidden" name="id" value="<?php echo $id ?>" />
    <input type="hidden" name="comment1" value="<?php echo $comment ?>" />
    </form>
    </div>
</body>

</html>