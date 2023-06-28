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
      <th scope="col">Detal</th>
      <th scope="col">Ilosc Wymagana/Zrealizowana</th>
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
      if($data['ilosc']==0 or $data['ilosc']==''){
        $szer = 0;
      }else{
        $szer = $data['AmountDone']/$data['ilosc'] *100;
      }
      
    ?>
  <tr>
        <td><?php echo $data['ProjectName']; ?></td>
        <td><?php echo $data['zespol']; ?></td>
        <td><?php echo $data['Name']; ?></td>
        <td>
        <div class="progress">
        <?php if($szer <= 100){ ?>
          <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo  $data['AmountDone']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $data['ilosc']; ?>'><?php echo $data['ilosc']."/".$data['AmountDone']; ?></div>
        <?php } else{ ?>
          <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szer; ?>%;' aria-valuenow="<?php echo  $data['AmountDone']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $data['ilosc']; ?>'><?php echo $data['ilosc']."/".$data['AmountDone']; ?></div>
        <?php } 
        ?>
      </td>
        <td><?php echo $data['machine']; ?></td>
        <td><?php echo $data['Profil']; ?></td>
        <td><?php echo $data['Material']; ?></td>
        <td><?php echo $data['Dlugosc']; ?></td>
        <td><?php echo $data['SawLength']; ?></td>
        <td><?php echo $data['Ciezar']; ?></td>
        <td><?php echo $data['Calk_ciez']; ?></td>
        <td><?php echo $data['Uwaga']; ?></td>
        <td><?php if($data['ModificationDate'] != "") {echo $data['ModificationDate']->format('Y-m-d H:i:s');} ?>
</td>
  </tr>
  <?php }  ?>

<?php 
require_once("messer.php");

 while ($datamesser = sqlsrv_fetch_array($datasmesser, SQLSRV_FETCH_ASSOC)) {
  if($datamesser['zapotrzebowanie']==0 or $datamesser['zapotrzebowanie']==''){
    $szermesser = 0;
  }else{
    $szermesser = $datamesser['Complet']/$datamesser['zapotrzebowanie'] *100;
  }
?>
<tr>
    <td ><?php echo $datamesser['Projekt']; ?></td>
    <td><?php echo $datamesser['Zespol']; ?></td>
    <td ><?php echo $datamesser['PartName']; ?></td>

    <td >
      <div class="progress">
        <?php if($szermesser<=100){ ?>
          <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow="<?php echo  $datamesser['Complet']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $datamesser['zapotrzebowanie']; ?>'><?php echo $datamesser["zapotrzebowanie"]."/".$datamesser['Complet']; ?></div>
        <?php } else { ?>
          <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow='<?php echo  $datamesser['Complet']; ?>' aria-valuemin='0' aria-valuemax='<?php echo $datamesser["zapotrzebowanie"]; ?>'><?php echo $datamesser["zapotrzebowanie"]."/".$datamesser['Complet']; ?></div>
      <?php  } 
        ?>
  </div>
  </td>
    <td><?php echo $datamesser['machine']; ?></td>
    <td><?php echo $datamesser['grubosc']; ?></td>
    <td colspan="3"><?php echo $datamesser['material']; ?></td>
    <td colspan="4" style="text-align:right;"><?php if($datamesser['DataWykonania'] != "") {echo $datamesser['DataWykonania']->format('Y-m-d H:i:s');} ?></td>
</tr>
<?php } ?>

<?php 
require_once("othersql.php");

 while ($dataot = sqlsrv_fetch_array($dataother, SQLSRV_FETCH_ASSOC)) {
  if($dataot['complet']==0 or $dataot['complet']==''){
    $szermesser = 0;
  }else{
    $szermesser = $dataot['complet']/$dataot['ilosc'] *100;
  }
?>
<?php if($szermesser>=100){ ?>
  <tr>
<?php } else { ?>
  <tr ondblclick="openLoginDialog(this)">
  <?php } ?>
    <td id="project"><?php echo $dataot['ProjectName']; ?></i></td>
    <td id="zespol"><?php if($dataot['status']==1){ echo $dataot['aggregated_zespol']." <i class='bi bi-exclamation-triangle-fill text-danger'>";} else{echo $dataot['aggregated_zespol'];} ?></td>
    <td id="detal"><?php echo $dataot['Name']; ?></td>
    <td >
      <div class="progress">
      <?php if($szermesser<=100){ ?>
          <div class='progress-bar bg-success' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow="<?php echo  $dataot['complet']; ?>" aria-valuemin='0' aria-valuemax='<?php echo $$dataot['ilosc']; ?>'><?php echo $dataot['ilosc']."/".$dataot['complet']; ?></div>
        <?php } else { ?>
          <div class='progress-bar bg-warning' role='progressbar' style='width:<?php echo $szermesser; ?>%;' aria-valuenow='<?php echo  $dataot['complet']; ?>' aria-valuemin='0' aria-valuemax='<?php echo $dataot['ilosc']; ?>'><?php echo $dataot['ilosc']."/".$dataot['complet']; ?></div>
       </div>
  </div>
  <?php } ?>
  </td>
    <td><?php echo $dataot['machine']; ?></td>
    <td><?php echo $dataot['profil']; ?></td>
    <td><?php echo $dataot['material']; ?></td>
    <td><?php echo $dataot['dlugosc']; ?></td>
    <td><?php echo $dataot['dlugosc_zrea']; ?></td>
    <td><?php echo $dataot['ciezar']; ?></td>
    <td><?php echo $dataot['calk']; ?></td>
    <td><?php echo $dataot['uwaga']; ?>, <?php echo $dataot['wykonal']; ?></td>
    <td><?php if($dataot['data'] != "") {echo $dataot['data']->format('Y-m-d H:i:s');} ?></td>
</tr>
<?php } ?>
  </tbody>
</table>
<div class="modal fade" id="mymodal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edycja projektu</h4>
                </div>
                <form method="POST" action="zapisze_dane.php" id="myForm">
                <div class="modal-body">
                Nazwa projektu: <label id="projectName" name="projectName"></label><br />
                <input type="hidden" name="project">
                Zespół: <label id="zespolName" name="zespolName"></label><br />
                Detal: <label id="detalName" name="detalName"></label>
                <input type="hidden" name="detal">
                    <br />
                    <input class="form-control" type="number" placeholder="Ilość" name="ilosc">
                    <br />
                    <input class="form-control" type="number" placeholder="Długość" name="dlugosc">
                    <br />
                    <select class="form-control" name="osoba" required>
                        <option value=" " selected>Wykonał</option>
                        <option value="SYLWESTER WOZNIAK">SYLWESTER WOZNIAK</option>
                        <option value="MARCIN MICHAS">MARCIN MICHAS</option>
                        <option value="LUKASZ PASEK">LUKASZ PASEK</option>
                        <option value="ARTUR BEDNARZ">ARTUR BEDNARZ</option>
                        <option value="DARIUSZ MALEK">DARIUSZ MALEK</option>
                    </select>
                    <br />
                    <select class="form-control" name="maszyna" required>
                        <option value="Recznie" selected>Recznie</option>
                        <option value="Kooperacyjnie">Kooperacyjnie</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button  type="Submit" name="save" class="btn btn-default" value='piece'>Zapisz</button >
                    <button  type="Submit" name="save" class="btn btn-default" value='all'>Zakończ</button >
                    <button  type="Submit" name="save" class="btn btn-default" value='pilne'>Status</button >
                    
                </div>
                </form>
            </div>
        </div>
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

function openLoginDialog(row) {
            var projectName = row.querySelector('#project').innerHTML;
            var zespolName = row.querySelector('#zespol').innerHTML;
            var detalName = row.querySelector('#detal').innerHTML;

            var projectNameDiv = document.querySelector('#mymodal #projectName');
            var zespolNameDiv = document.querySelector('#mymodal #zespolName');
            var detalNameDiv = document.querySelector('#mymodal #detalName');
            
            projectNameDiv.innerHTML =  projectName;
            zespolNameDiv.innerHTML =  zespolName;
            detalNameDiv.innerHTML =  detalName;

            document.getElementById("myForm").elements.namedItem("project").setAttribute("value", projectName);
            document.getElementById("myForm").elements.namedItem("detal").setAttribute("value", detalName);
            
            $('#mymodal').modal('show');
        }


</script>
</html>