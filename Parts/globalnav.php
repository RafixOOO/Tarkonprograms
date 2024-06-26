<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <!-- Container wrapper -->
    <fync class="navbar-brand" style="margin-left:2%;">Tarkon programs <sup>2.5</sup></fync>
    <button style="margin-right:2%;" class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
        <img src="../static/menu.svg"></img>
    </button>
    <div class="container-xxl">
        <!-- Navbar brand -->


        <!-- Collapsible wrapper -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left links -->
            <ul class="navbar-nav me-auto d-flex flex-row mt-3 mt-lg-0">
                <?php
                // Lista zablokowanych adresów IP
                $blockedIPs = array("10.100.102.126");

                // Pobierz aktualny adres IP użytkownika
                $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

                // Sprawdź, czy adres IP użytkownika jest zablokowany
                if (!in_array($userIP, $blockedIPs)) {
                    ?>
                    <li class="nav-item text-center mx-2 mx-lg-1">
                        <?php if (isLoggedIn()) { ?>
                            <a class="nav-link" aria-current="page" href="../index.php">
                                Strona główna
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if (isUserMesser()) { ?>
                    <li class="nav-item dropdown text-center mx-2 mx-lg-1">
                        <func href="#" class="nav-link dropdown-toggle" id="navbarDropdown1" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Messer
                        </func>
                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown1">
                            <li><a class="dropdown-item" href="../messer/main.php">Aktualne</a></li>
                            <li><a class="dropdown-item" href="../messer/wykonane.php">Zakończone</a></li>
                            <li><a class="dropdown-item" href="../messer/archiwum.php">Archiwalne</a></li>
                            <li><a class="dropdown-item" href="../messer/magazyn.php">Magazyn</a></li>
                            <li><a class="dropdown-item" href="../messer/magazynarch.php">Magazyn Archiwum</a></li>

                        </ul>
                    </li>
                    <li class="nav-item text-center mx-2 mx-lg-1">
                        <a class="nav-link" aria-current="page" href="../v200/main.php">
                            V200
                        </a>
                    </li>
                <?php } ?>
                <?php if (isLoggedIn()) { ?>
                    <li class="nav-item dropdown text-center mx-2 mx-lg-1">
                        <func class="nav-link active dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Parts
                        </func>

                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="main.php">Programy</a></li>
                            <li><a class="dropdown-item" href="dozrobienia.php">Gotowe</a></li>
                            <?php if (isUserParts()) { ?>
                                <li><a class="dropdown-item" href="upload.php">Wyślij</a></li>
                            <?php } ?>
                        </ul>

                    </li>
                <?php } ?>
                <?php if (isUserCutlogic()) { ?>
                    <li class="nav-item text-center mx-2 mx-lg-1">
                        <a class="nav-link active" aria-current="page" href="../cutlogic/main.php">
                            CutLogic
                        </a>
                    </li>
                <?php } ?>
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                function isActive($page)
                {
                    global $current_page;
                    return $current_page === $page ? 'active' : '';
                }

                ?>
                <li class="nav-item text-center mx-2 mx-lg-1">
                    <a class="nav-link <?php echo isActive('main.php'); ?>" aria-current="page" href="main.php">
                        Programy
                    </a>
                </li>
                <li class="nav-item text-center mx-2 mx-lg-1">
                    <a class="nav-link <?php echo isActive('dozrobienia.php'); ?>" aria-current="page"
                       href="dozrobienia.php">
                        Gotowe
                    </a>
                </li>
            </ul>
            <!-- Left links -->
            <!-- Right links -->
            <ul class="navbar-nav ms-auto d-flex flex-row mt-3 mt-lg-0">
                <li class="nav-item dropdown text-center mx-2 mx-lg-1">
                    <fync class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-bs-auto-close="outside"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">

                        <?php if (isLoggedIn()) { ?>
                            <?php echo $_SESSION['imie_nazwisko']; ?>
                        <?php } else { ?>
                            Zaloguj się

                        <?php } ?>
                    </fync>
                    <!-- Dropdown menu -->

                    <ul class="dropdown-menu dropdown-menu-dark" style="left: -35%;" aria-labelledby="navbarDropdown">

                        <li class="dropstart"><fync class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown2"
                                                 role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                 aria-expanded="false">Ustawienia</fync>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown2">
                                <?php if (isLoggedIn()) { ?>
                                    <li><a class="dropdown-item" href="../password.php">Zmień hasło</a></li>
                                    <li><a class="dropdown-item" href="../username.php">Zmień nazwę użytkownika</a></li>
                                <?php } ?>
                                <li><fync class="dropdown-item" id="darkModeButton" href="#">Tryb ciemny</fync></li>
                            </ul>

                            <?php if (isUserAdmin()) { ?>
                        <li class="dropstart"><fync class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown2"
                                                 role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                 aria-expanded="false">Panel admina</fync>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown2">
                                <li><a class="dropdown-item" href="../zarzadzaj.php">Zarządzaj</a></li>
                                <li><a class="dropdown-item" href="../logi.php">Logi</a></li>
                            </ul>
                        </li>

                        <?php } ?>
                        <?php
                        // Lista zablokowanych adresów IP
                        $blockedIPs = array("10.100.102.126");

                        // Pobierz aktualny adres IP użytkownika
                        $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

                        // Sprawdź, czy adres IP użytkownika jest zablokowany
                        ?>
                        <li class="dropdown-divider"></li>
                        <?php if (isLoggedIn()) { ?>
                            <li><a class="dropdown-item" href="../logout.php">Wyloguj się</a></li>
                        <?php } ?>
                        <?php if (!isLoggedIn()) { ?>
                            <li><a class="dropdown-item" href="../login.php">Zaloguj się</a></li>
                        <?php } ?>
                    </ul>

                </li>
            </ul>
            <!-- Right links -->
        </div>
        <!-- Collapsible wrapper -->
    </div>
    <!-- Container wrapper -->
</nav>
<br/>
<!-- Navbar -->