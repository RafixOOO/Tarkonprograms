<!DOCTYPE html>

<html>

<head>

  <?php 
  require_once('globalhead.php') ;
  require_once('../auth.php');
  ?>

<style>
    tr.hide-table-padding td {
  padding: 0;
  }

  .expand-button {
    position: relative;
  }

  .accordion-toggle .expand-button:after
  {
    position: absolute;
    left:.75rem;
    top: 50%;
    transform: translate(0, -50%);
    content: '-';
  }
  .accordion-toggle.collapsed .expand-button:after
  {
    content: '+';
  }
    </style>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" style="max-height:800px;" id="error-container">
<?php require_once('globalnav.php') ?>
  <div class="container-xl">
  <form method="get" action="">
          <div class="input-group">
            <input type="text" class="form-control" name="keywords" value="" placeholder="Nazwa projektu..."> <button class="btn btn-primary" type="submit">Szukaj</button>
          </div>
          </form>
            <br /><br />
            <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Zaspół</th>
            <th scope="col">Ilość</th>
          </tr>
        </thead>
        <tbody>
          <tr class="accordion-toggle collapsed"
            data-toggle="collapse"
            data-target="#collapseOne" 
            aria-controls="collapseOne"
          >
            <td class="expand-button"></td>
            <td>Cell</td>
            <td>Cell</td>
          </tr>
          <tr class="hide-table-padding">
            <td></td>
            <td colspan="2">
              <div id="collapseOne" class="collapse p-3">
                <div class="row">
                  <div class="col-2">label</div>
                  <div class="col-6">value 1</div>
                </div>
                <div class="row">
                  <div class="col-2">label</div>
                  <div class="col-6">value 2</div>
                </div>
                <div class="row">
                  <div class="col-2">label</div>
                  <div class="col-6">value 3</div>
                </div>
                <div class="row">
                  <div class="col-2">label</div>
                  <div class="col-6">value 4</div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
</div>
</div>
<script>
    // Dodajemy obsługę kliknięć na wiersze główne (akordeon)
    document.addEventListener('DOMContentLoaded', function() {
      var toggleElements = document.querySelectorAll('.accordion-toggle');
      toggleElements.forEach(function(element) {
        element.addEventListener('click', function() {
          var targetId = this.getAttribute('data-target');
          var targetCollapse = document.querySelector(targetId);
          targetCollapse.classList.toggle('show'); // Dodajemy/zdejmujemy klasę "show", aby rozwijać/zwijać akordeon
        });
      });
    });
  </script>
</body>
</html>