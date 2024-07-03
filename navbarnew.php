<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="assets/css/plugins.min.css"/>
<link rel="stylesheet" href="assets/css/kaiadmin.min.css"/>
<script src="assets/js/plugin/webfont/webfont.min.js"></script>
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
      urls: ["assets/css/fonts.min.css"],
    },
    active: function () {
      sessionStorage.fonts = true;
    },
  });
</script>
<div class="wrapper sidebar_minimize">
  <!-- Sidebar -->
  <div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
      <!-- Logo Header -->
      <div class="logo-header" data-background-color="dark">
        <a href="index.html" class="logo">
          <img
            src="assets/img/logo1.png"
            alt="navbar brand"
            class="navbar-brand"
            height="220"
            width="220"
          />
        </a>
      </div>
      <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
        <ul class="nav nav-secondary">
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Profil</h4>
          </li>
          <li class="nav-item">
            <a href="#">
              <i class="fa fa-user"></i>
              <p>Zaloguj się</p>
            </a>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#dane">
              <i class="fa fa-indent"></i>
              <p>Dane</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="dane">
              <ul class="nav nav-collapse">
                <li>
                  <a href="">
                    <span class="sub-item">Nazwa użytkownika</span>
                  </a>
                </li>
                <li>
                  <a href="">
                    <span class="sub-item">Hasło</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#settings">
              <i class="fa fa-cogs"></i>
              <p>ustawienia</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="settings">
              <ul class="nav nav-collapse">
                <li>
                  <a href="">
                    <span class="sub-item">Tryb ciemny</span>
                  </a>
                </li>
              </ul>
            </div>
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
                  <a href="">
                    <span class="sub-item">Programy</span>
                  </a>
                </li>
                <li>
                  <a href="">
                    <span class="sub-item">Gotowe</span>
                  </a>
                </li>
                <li>
                  <a href="">
                    <span class="sub-item">Wyślij</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a href="#">
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
                  <a href="sidebar-style-2.html">
                    <span class="sub-item">Aktualne</span>
                  </a>
                </li>
                <li>
                  <a href="icon-menu.html">
                    <span class="sub-item">Zakończone</span>
                  </a>
                </li>
                <li>
                  <a href="icon-menu.html">
                    <span class="sub-item">Archiwalne</span>
                  </a>
                </li>
                <li>
                  <a href="icon-menu.html">
                    <span class="sub-item">Magazyn</span>
                  </a>
                </li>
                <li>
                  <a href="icon-menu.html">
                    <span class="sub-item">Magazyn Archiwum</span>
                  </a>
                </li>

              </ul>
            </div>
          </li>
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
          <ul class="nav nav-secondary">
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">Panel administracyjny</h4>
            </li>
            <li class="nav-item">
              <a href="#">
                <i class="fa fa-database"></i>
                <p>Role</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#">
                <i class="fa fa-desktop"></i>
                <p>Logi systemowe</p>
              </a>
            </li>
          </ul>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- End Sidebar -->
<script src="assets/js/core/jquery-3.7.1.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- jQuery Sparkline -->
<script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Kaiadmin JS -->
<script src="assets/js/kaiadmin.min.js"></script>