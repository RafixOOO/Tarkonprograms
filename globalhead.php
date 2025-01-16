<title>Tarkonprograms</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="shortcut icon" href="static/clipboard-data.svg">
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

<link rel="stylesheet" href="static/toastr.min.css">
<script src="static/jquery.min.js"></script>
<script src="static/jquery-ui.min.js"></script>
<script src="static/toastr.min.js"></script>
<script src="static/jquery-3.6.0.min.js"></script>
<script src="static/jquery.min.js"></script>
<script src="static/darkmode-js.min.js"></script>
<script src="blad.js"></script>
<?php if(isLoggedIn()){ ?>
<script>
 window.addEventListener('load', function() {
  var darkModeButton = document.getElementById('darkModeButton');
  var darkmode = new Darkmode();

  darkModeButton.addEventListener('click', function() {
    darkmode.toggle();
  });

  // Ukrycie przycisku dostarczanego przez bibliotekę Darkmode
  var darkmodeToggleElement = document.querySelector('.darkmode-toggle');
  if (darkmodeToggleElement) {
    darkmodeToggleElement.style.display = 'none';
  }
});
</script>
<?php } ?>
<style>
  .darkmode-background{
  background-color: #f8f9fd !important;
}
    #button-container {
      position: fixed;
      top: 0;
      left: 0;
      padding: 10px;
    }
    </style>