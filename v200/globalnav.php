<!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../assets/css/plugins.min.css"/>
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css"/>
<script src="../assets/js/plugin/webfont/webfont.min.js"></script>
<script>
  WebFont.load({
    google: { families: ["Public Sans:300,400,500,600,700"] },
    custom: {
      families: [
        "Font Awesome 5 Solid",
        "Font Awesome 5 Regular",
        "Font Awesome 5 Brands",
        "simple-line-icons",
      ],
      urls: ["../assets/css/fonts.min.css"],
    },
    active: function () {
      sessionStorage.fonts = true;
    },
  });
</script>
<?php if(isSidebar()==0){ ?>
<div class="wrapper" style="padding-bottom: 0; width:0; height:0;">
  <?php }else if(isSidebar()==1){ ?>
    <div class="wrapper sidebar_minimize" style="padding-bottom: 0; width:0; height:0;">
      <?php } ?>
  <!-- Sidebar -->
  <div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
      <!-- Logo Header -->
      <div class="logo-header" data-background-color="dark">
      
        <a href="#" class="logo" style="position: relative;
      display: inline-block;";>
        <img
            src="../assets/img/logo.svg"
            alt="navbar brand"
            class="navbar-brand"
            height="90"
            width="220"
          />
          <div style="position: absolute;
      top: 20%;
      left: 20%;
      transform: translate(-20%, -520%);
      width: 20%;  /* Szerokość klikalnego obszaru (np. 50% szerokości obrazka) */
      height: 20%; /* Wysokość klikalnego obszaru (np. 50% wysokości obrazka) */
      cursor: pointer;"></div>
        </a>
      </div>
      <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
        <ul class="nav nav-secondary">
        <li class="nav-item">
            <a href="../index.php">
              <i class="fa fa-home"></i>
              <p>Strona główna</p>
            </a>
          </li>
        </ul>
        <ul class="nav nav-secondary">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Aplikacje</h4>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#parts">
              <i class="fas fa-layer-group"></i>
              <p>Parts</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="parts">
              <ul class="nav nav-collapse">
                <li>
                  <a href="../parts/main.php">
                    <span class="sub-item">Projekty</span>
                  </a>
                </li>
                <li>
                  <a href="../parts/dozrobienia.php">
                    <span class="sub-item">Wykonane detale</span>
                  </a>
                </li>
                <?php if(isUserParts()){ ?>
                <li>
                  <a href="../parts/upload.php">
                    <span class="sub-item">Wczytaj detale</span>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a href="main.php">
              <i class="fas fa-table"></i>
              <p>V200</p>
            </a>
          </li>

          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#sidebarLayouts">
              <i class="fas fa-th-list"></i>
              <p>Messer</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="sidebarLayouts">
              <ul class="nav nav-collapse">
                <li>
                  <a href="../messer/main.php">
                    <span class="sub-item">Programy</span>
                  </a>
                </li>
                <li>
                  <a href="../messer/wykonane.php">
                    <span class="sub-item">Zakończone Programy</span>
                  </a>
                </li>
                <li>
                  <a href="../messer/archiwum.php">
                    <span class="sub-item">Programy Archiwum </span>
                  </a>
                <li>
                  <a href="../messer/messersoft.php">
                    <span class="sub-item">Messer Soft</span>
                  </a>
                </li>

              </ul>

              <li class="nav-item">
            <a data-bs-toggle="collapse" href="#sidebarLayouts1">
            <i class="fas fa-pallet"></i>
              <p>Magazyn</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="sidebarLayouts1">
              <ul class="nav nav-collapse">
              <li>
                  <a href="../magazyn/main.php">
                    <span class="sub-item">Lista</span>
                  </a>
                </li>
                <li>
                  <a href="../magazyn/magazynarch.php">
                    <span class="sub-item">Archiwum</span>
                  </a>
                </li>

              </ul>
              
            </div>
            </li>
            <!--<li class="nav-item">
            <a href="cutlogic/main.php">
              <i class="fa fa-laptop"></i>
              <p>Cutlogic (W Rozbudowie)</p>
            </a>
          </li>-->
          <li class="nav-item">
            <a href="https://urlop.local/" target="_blank">
              <i class="fa fa-bicycle"></i>
              <p>Urlopy</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="https://hrappka.budhrd.eu/auth/login?prev_path=/index/schedule" target="_blank">
              <i class="fa fa-calendar"></i>
              <p>Hrappka</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="https://pp.timaco.pl/" target="_blank">
              <i class="fa fa-check"></i>
              <p>Punktualnik</p>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
    $(".btn-toggle").click(function(){
      $.ajax({
        url: "../sidebar.php", // Ścieżka do pliku PHP
        type: "POST", // Wysyłamy żądanie POST
        data: { status: "nowyStatus" }, // Możesz przekazać dowolne dane, np. status
        success: function(response){
          // Pokaż komunikat o sukcesie
          location.reload();
          $("#status-message").html("<p>Status został zaktualizowany pomyślnie.</p>");
        },
        error: function(xhr, status, error){
          // Pokaż komunikat o błędzie
          $("#status-message").html("<p>Wystąpił błąd: " + error + "</p>");
        }
      });
    });
  });
</script>
<!-- End Sidebar -->
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- jQuery Sparkline -->
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>