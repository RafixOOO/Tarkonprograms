<!DOCTYPE html>
<html>
<head>
    
    <?php require_once('globalhead.php') ?>
</head>
<body class = "bg-secondary p-2 text-dark bg-opacity-25">
    <div class="container-fluid">
    <?php require_once('globalnav.php') ?>
<table id="myTable" class="table table-striped table-bordered">
  <thead>
    <tr>
      <th scope="col">Projekt</th>
      <th scope="col">Zespoły</th>
      <th scope="col">Funkcja</th>
      <th scope="col">Ilosc</th>
      <th scope="col">Ilosc Zrealizowana</th>
      <th scope="col">Maszyna</th>
      <th scope="col">Wymiar</th>
      <th scope="col">Materiał</th>
      <th scope="col">Długość</th>
      <th scope="col">Długość Zrealizowana</th>
      <th scope="col">Ciężar</th>
      <th scope="col">Całkowity Ciężar</th>
      <th scope="col">Uwaga</th>
      <th scope="col">Data Operacji</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    require_once("v630.php");
    ?>
    <?php
     while ($data = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
    ?>
  <tr>
        <td><?php echo $data['ProjectName']; ?></td>
        <td><?php echo $data['zespol']; ?></td>
        <td><?php echo $data['Name']; ?></td>
        <td><?php echo $data['ilosc']; ?></td>
        <td><?php echo $data['AmountDone']; ?></td>
        <td><?php echo $data['machine']; ?></td>
        <td><?php echo $data['Profil']; ?></td>
        <td><?php echo $data['Material']; ?></td>
        <td><?php echo $data['Dlugosc']; ?></td>
        <td><?php echo $data['SawLength']; ?></td>
        <td><?php echo $data['Ciezar']; ?></td>
        <td><?php echo $data['Calk_ciez']; ?></td>
        <td><?php echo $data['Uwaga']; ?></td>
        <td><?php echo $data['ModificationDate']->format('Y-m-d H:i:s') ; ?></td>
  </tr>
  <?php } ?>

<?php 
require_once("messer.php");

 while ($datamesser = sqlsrv_fetch_array($datasmesser, SQLSRV_FETCH_ASSOC)) {
?>
<tr>
    <td><?php echo $datamesser['Projekt']; ?></td>
    <td><?php echo $datamesser['PartName']; ?></td>
    <td><?php echo $datamesser['program']; ?></td>

    <td><?php echo $datamesser['zapotrzebowanie']; ?></td>
    <td><?php echo $datamesser['Complet']; ?></td>
    <td><?php echo $datamesser['machine']; ?></td>
    <td><?php echo $datamesser['grubosc']; ?></td>
    <td colspan="3"><?php echo $datamesser['material']; ?></td>
    <td colspan="4" style="text-align:right;"><?php if($datamesser['DataWykonania'] != "") {echo $datamesser['DataWykonania']->format('Y-m-d H:i:s');} ?></td>
</tr>
<?php } ?>
  </tbody>
</table>
</div>
</body>
<script>

document.getElementById("searchInput").addEventListener("keyup", function() {
  let input = this.value.toLowerCase();
  let table = document.getElementById("myTable");
  let rows = table.getElementsByTagName("tr");

  if (input === "") {
    for (let i = 1; i < rows.length; i++) {
      rows[i].style.display = "";
    }
    return;
  }

  for (let i = 1; i < rows.length; i++) {
    let rowData = rows[i].getElementsByTagName("td");
    let inputs = input.split(",").map(value => value.trim());
    let foundCount = 0;

    for (let j = 0; j < rowData.length; j++) {
      let cellText = rowData[j].textContent.toLowerCase();

      for (let k = 0; k < inputs.length; k++) {
        let currentInput = inputs[k];
        if (cellText.indexOf(currentInput) !== -1) {
          foundCount++;
          break;
        }
      }
    }

    if (foundCount === inputs.length) {
      rows[i].style.display = "";
    } else {
      rows[i].style.display = "none";
    }
  }
});

const headers = document.querySelectorAll("#myTable th");


headers.forEach(header => {
  header.addEventListener("click", () => {
    const table = header.closest("table");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);


    const sortDirection = header.getAttribute("data-sort");


    const newSortDirection = sortDirection === "asc" ? "desc" : "asc";

 
    header.setAttribute("data-sort", newSortDirection);


    headers.forEach(h => h.textContent = h.textContent.replace(" ▲", "").replace(" ▼", ""));

   
    header.textContent += newSortDirection === "asc" ? " ▲" : " ▼";

   
    const sortedRows = rows.sort((a, b) => {
      const cellA = a.querySelectorAll("td")[columnIndex].textContent.toLowerCase();
      const cellB = b.querySelectorAll("td")[columnIndex].textContent.toLowerCase();

      if (newSortDirection === "asc") {
        return cellA.localeCompare(cellB);
      } else {
        return cellB.localeCompare(cellA);
      }
    });

   
    rows.forEach(row => tbody.removeChild(row));

    
    sortedRows.forEach(row => tbody.appendChild(row));
  });
});




</script>
</html>