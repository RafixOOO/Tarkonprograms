<!DOCTYPE html>
<html lang="pl">
<head>
<?php require_once 'auth.php'; ?>

<?php require_once("globalhead.php"); ?>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
    <!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<div class="container">
    <div class="row">
        <div class="col min-vh-100 py-3">
            <!-- toggler -->
            

            <?php
if(isUserAdmin()){
$logFilePath = 'dziennik.log';

$logLines = file($logFilePath);
$logLines = array_reverse($logLines);

echo "<table id='example' class='table table-stripped'>";
echo "<thead><tr>
<th style='width:15em;'>Data</th>
<th style='width:15em;'>Użytkownik</th>
<th>Operacja</th>
</tr></thead><tbody>";

foreach ($logLines as $line) {
    $logData = explode(",", $line);
    $date = $logData[0];
    $username = $logData[1];
    $operation = implode(",", array_slice($logData, 2));

    echo "<tr>
    <td>$date</td>
    <td>$username</td>
    <td>$operation</td>
    </tr>";
}

echo "</tbody></table>";
}
?>
</div>
</div>
</div>
<?php require_once("globalnav.php"); ?>
</body>
<script src="static/jquery.dataTables.min.js"></script>
<script src="static/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function(){
        $('#example').DataTable();
    });
</script>

</html>
  