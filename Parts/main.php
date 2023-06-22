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
      <th scope="col">Pozycja</th>
      <th scope="col">Ilosc</th>
      <th scope="col">Ilosc Zrobiona</th>
      <th scope="col">Machine</th>
      <th scope="col">Profil</th>
      <th scope="col">Materiał</th>
      <th scope="col">Długość</th>
      <th scope="col">Długość zrobiona</th>
      <th scope="col">Ciężar</th>
      <th scope="col">Całkowity Ciężar</th>
      <th scope="col">Uwaga</th>
      <th scope="col">Data Modyfikacji</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    require_once("dbconnect.php");
    $sql = "Select Distinct 
		b.[ProjectName]
	   ,STRING_AGG(p.[Zespol],', ') as zespol
	   ,b.[Name]
      ,Sum(p.[Ilosc]) as ilosc
	  ,b.[AmountDone] 
	  ,		'V630' as machine
	  ,p.[Profil]
      ,p.[Material]
      ,p.[Dlugosc]
	  ,b.[SawLength]
      ,p.[Ciezar]
      ,p.[Calk_ciez]
      ,p.[Uwaga]
	  ,b.[ModificationDate]
       from [PartCheck].[dbo].[Product_back] as b LEFT JOIN [PartCheck].[dbo].[Parts] as p ON b.[Name] = p.[Pozycja] 
	   group by b.[AmountDone],b.[Name],p.[Profil],p.[Material],p.[Dlugosc],p.[Ciezar],p.[Calk_ciez],p.[Uwaga],b.[ModificationDate],b.[SawLength],b.[ProjectName]";
    $datas = sqlsrv_query($conn, $sql); ?>
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
  <?php }
  
  
  ?>
  </tbody>
</table>
</div>
</body>
<script>

document.getElementById("searchInput").addEventListener("keyup", function() {
  let input = this.value.toLowerCase().split(",");
  let table = document.getElementById("myTable");
  let rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    let rowData = rows[i].getElementsByTagName("td");
    let found = false;

    for (let j = 0; j < rowData.length; j++) {
      let cellText = rowData[j].textContent.toLowerCase();

      let allInputsFound = true;
      for (let k = 0; k < input.length; k++) {
        let currentInput = input[k].trim();
        if (cellText.indexOf(currentInput) === -1) {
          allInputsFound = false;
          break;
        }
      }

      if (allInputsFound) {
        found = true;
        break;
      }
    }

    rows[i].style.display = found ? "" : "none";
  }
});

// Pobierz wszystkie nagłówki kolumn
const headers = document.querySelectorAll("#myTable th");

// Przypisz obsługę zdarzenia kliknięcia do każdego nagłówka kolumny
headers.forEach(header => {
  header.addEventListener("click", () => {
    const table = header.closest("table");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    // Pobierz indeks kolumny na podstawie indeksu nagłówka kolumny
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);

    // Pobierz kierunek sortowania z atrybutu "data-sort"
    const sortDirection = header.getAttribute("data-sort");

    // Zmiana kierunku sortowania po kliknięciu
    const newSortDirection = sortDirection === "asc" ? "desc" : "asc";

    // Zaktualizuj atrybut "data-sort" na nagłówku kolumny
    header.setAttribute("data-sort", newSortDirection);

    // Usuń istniejący znak sortowania we wszystkich nagłówkach kolumn
    headers.forEach(h => h.textContent = h.textContent.replace(" ↑", "").replace(" ↓", ""));

    // Dodaj znak sortowania w zależności od kierunku sortowania
    header.textContent += newSortDirection === "asc" ? " ↑" : " ↓";

    // Sortowanie danych wierszy
    const sortedRows = rows.sort((a, b) => {
      const cellA = a.querySelectorAll("td")[columnIndex].textContent.toLowerCase();
      const cellB = b.querySelectorAll("td")[columnIndex].textContent.toLowerCase();

      if (newSortDirection === "asc") {
        return cellA.localeCompare(cellB);
      } else {
        return cellB.localeCompare(cellA);
      }
    });

    // Usuń istniejące wiersze z tabeli
    rows.forEach(row => tbody.removeChild(row));

    // Dodaj posortowane wiersze do tabeli
    sortedRows.forEach(row => tbody.appendChild(row));
  });
});




</script>
</html>