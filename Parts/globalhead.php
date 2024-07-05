<title>Parts</title>
<meta charset ="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../static/toastr.min.css">
<link rel="shortcut icon" href="../static/clipboard-data.svg">
<script src="../static/jquery.min.js"></script>
<script src="../static/jquery-ui.min.js"></script>
<script src="../static/toastr.min.js"></script>
<script src="../static/jquery-3.6.0.min.js"></script>
<script src="../blad.js"></script>
<script src="../static/chart.js"></script>
<script src="../static/darkmode-js.min.js"></script>
<script src="../static/popper.min.js"></script>
<script src="../static/chosen.jquery.min.js"></script>
<link href="../static/chosen.min.css" rel="stylesheet"/>
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
<style>
#button-container {
      position: fixed;
      top: 0;
      left: 0;
      padding: 10px;
    }
::-webkit-scrollbar{
  width: 16px;
}
::-webkit-scrollbar-thumb{
  border-radius: 8px;
  border: 3px solid transparent;
  background-clip: content-box;
  background-color: #060b9a;
}
    </style>
