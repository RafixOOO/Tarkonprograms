<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'globalhead.php'; 
    require_once('../auth.php');?>
    <style>
      tr.selected {
    background-color: #cce5ff; /* Kolor tła dla zaznaczonych wierszy */
}
      </style>
</head>
<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php include 'globalnav.php'; ?>
<?php 
require_once 'cutlogic.php';
?>

<table id="myTable" class="table table-sm table-hover table-striped table-bordered" style="font-size: calc(9px + 0.390625vw)">


<thead>
  <tr>
    <th scope="col">Program</th>
    <th scope="col">Projekt</th>
    <th scope="col">Opis</th>
    <th scope="col">Length</th>
    <th scope="col">Resztki</th>
    <th scope="col">Część</th>
    <th scope="col">Długość Części</th>
    <th scope="col">Liczba</th>
    <th scope="col">Opcje</th>
  </tr>
</thead>
<tbody>
<?php while ($row = sqlsrv_fetch_array($datacut, SQLSRV_FETCH_ASSOC)) {
?>  
<tr value="<?php echo $row['ID']; ?>">
<td><?php echo $row['PROGRAM']; ?></td>
<td><?php echo $row['PROJEKT']; ?></td>
<td><?php echo $row['OPIS']; ?></td>
<td><?php echo $row['DLUGOSC']; ?></td>
<td><?php echo $row['RESZTKI']; ?></td>
<td><?php echo $row['CZESC']; ?></td>
<td><?php echo $row['CZDLU']; ?></td>
<td><?php echo $row['CNT']; ?></td>
<td><?php if($row['checkpr'] == 1) { ?><Button class='btn btn-success btn-sm' disabled>Zakończono</Button><?php } else { ?><?php if(isUserCutlogic()) { ?><Button class='btn btn-primary btn-sm'>Zakończ</Button><?php } ?><?php } ?></td>
</tr>
  <?php } ?>
</tbody>
</table>
</body>
<script src="../static/jquery-3.7.0.js"></script>
<script src="../static/jquery.dataTables.min.js"></script>
<script src="../static/dataTables.bootstrap5.min.js"></script>
<script>

    $(document).ready(function() {
    $('.btn-primary').on('click', function() {
        var button = $(this);
        var rowId = button.closest('tr').attr('value'); // Pobierz ID z atrybutu 'value' rodzica przycisku

        // Wywołaj AJAX, aby zaktualizować dane w bazie danych
        $.ajax({
            url: 'updatecut.php', // Ścieżka do pliku PHP, który obsłuży aktualizację bazy danych
            method: 'POST',
            data: { rowId: rowId }, // Przesyłanie ID wiersza do serwera
            success: function(response) {
                console.log(response); // Wyświetl odpowiedź z serwera w konsoli przeglądarki
                button.text('Zakończono'); // Zmień tekst przycisku na "Zakończono"
                button.removeClass('btn-primary').addClass('btn-success'); // Zmień styl przycisku na zielony
                button.prop('disabled', true); // Zablokuj przycisk, aby uniknąć kolejnych kliknięć
            }
        });
    });
    $('#myTable').DataTable();
});
</script>
</html>