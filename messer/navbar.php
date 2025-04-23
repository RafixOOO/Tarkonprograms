<?php
// Pobierz pełny URL i usuń część z parametrami po znaku '?'
$currentUrl = strtok($_SERVER['REQUEST_URI'], '?');  // np. '/programs/Tarkonprograms/zarzadzaj.php'

// Zdefiniuj podstawowy breadcrumb (strona główna)
$breadcrumb = [
    'Strona główna' => '/programs/Tarkonprograms/index.php',  // Strona główna
];

// Usuń część '/programs/Tarkonprograms/' z URL, aby uzyskać resztę ścieżki
$relativeUrl = str_replace('/programs/Tarkonprograms/', '', $currentUrl);

// Jeśli ścieżka nie jest pusta i nie jesteśmy na stronie głównej
if (!empty($relativeUrl) && $relativeUrl !== 'index.php') {
    // Podziel resztę ścieżki na segmenty
    $segments = explode('/', $relativeUrl);
    $path = '/programs/Tarkonprograms';  // Ścieżka do katalogu "programs/Tarkonprograms"

    // Tworzenie breadcrumb dla pozostałych segmentów URL
    foreach ($segments as $segment) {
        // Jeśli segment zawiera '.php', usuń rozszerzenie '.php' i dodaj link
        if (strpos($segment, '.php') !== false) {
            $segment = str_replace('.php', '', $segment);
            $path .= '/' . $segment;
            $breadcrumb[ucwords(str_replace('-', ' ', $segment))] = $path;
        } else {
            // Jeśli to folder (bez '.php'), dodaj go do breadcrumb bez linku
            $path .= '/' . $segment;
            $breadcrumb[ucwords(str_replace('-', ' ', $segment))] = $path;
        }
    }
}
?>
<div class="main-header">
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom" style="margin-top: 0;">

<div class="container-fluid">

<ul class="navbar-nav topbar-nav ms-md-auto align-items-left">
<li class="nav-item max-auto">
    <ol class="breadcrumb">
    <?php
    foreach ($breadcrumb as $label => $url) {
        // Sprawdzamy, czy to ostatni element i nie robimy linku dla niego
        if ($url == end($breadcrumb)) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
        } else {
            // Dodaj link tylko jeśli to plik .php
            if (strpos($url, '.php') !== false) {
                echo '<li class="breadcrumb-item"><a href="' . $url . '">' . $label . '</a></li>';
            } else {
                echo '<li class="breadcrumb-item"><a href="' . $url . '/main.php">' . $label . '</li>';
            }
        }
    }
    ?>
</ol>
</li>
</ul>

    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
    <?php if(isUserAdmin()) { ?>
        <li class="nav-item topbar-icon dropdown hidden-caret">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
            <i class="fa fa-bars" style="color: black;"></i>

            </a>
            <div class="dropdown-menu quick-actions animated fadeIn">
                <div class="quick-actions-header">
                    <span class="title mb-1">Panel administracyjny</span>
                </div>
                <div class="quick-actions-scroll scrollbar-outer">
                    <div class="quick-actions-items">
                        <div class="row m-0">
                            <a class="col-6 col-md-4 p-0" href="..\logi.php">
                                <div class="quick-actions-item">
                                    <div class="avatar-item bg-info rounded-circle">
                                        <i class="fa fa-desktop"></i>
                                    </div>
                                    <span class="text">Logi systemowe</span>
                                </div>
                            </a>
                            <a class="col-6 col-md-4 p-0" href="..\zarzadzaj.php">
                                <div class="quick-actions-item">
                                    <div class="avatar-item bg-primary rounded-circle">
                                        <i class="fa fa-database"></i>
                                    </div>
                                    <span class="text">Role</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php } ?>
        <li class="nav-item topbar-user dropdown hidden-caret">
        <a class="dropdown-toggle profile-pic" 
   data-bs-toggle="<?php echo isLoggedIn() ? 'dropdown' : ''; ?>"
   href="<?php echo isLoggedIn() ? '#' : '..\login.php'; ?>" 
   aria-expanded="false">
   <div class="avatar-sm" style="width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #4CAF50; /* Kolor tła */
    color: #FFFFFF; /* Kolor tekstu */
    font-size: 20px;
    border-radius: 50%; /* Okrągły kształt */
    font-family: Arial, sans-serif;
    text-transform: uppercase;"><?php 
if (!empty($_SESSION['imie_nazwisko']) && isset($_SESSION['imie_nazwisko'][0])) {
    echo strtoupper($_SESSION['imie_nazwisko'][0]);
} else {
    echo "U";
}
?>
    </div>
    <span class="profile-username">
        <?php if(isLoggedIn()) { ?>
            <b><?php echo $_SESSION['imie_nazwisko']; ?></b>
        <?php } else { ?>
            <b>Zaloguj się</b>
        <?php } ?>
    </span>
</a>

<?php if(isLoggedIn()) { ?>
    <ul class="dropdown-menu dropdown-user animated fadeIn">
        <div class="dropdown-user-scroll scrollbar-outer">
            <li>
                <div class="user-box">
                <div class="avatar-lg" style="width: 25%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #4CAF50; /* Kolor tła */
    color: #FFFFFF; /* Kolor tekstu */
    font-size: 30px;
    border-radius: 50%; /* Okrągły kształt */
    font-family: Arial, sans-serif;
    text-transform: uppercase;"><?php 
if (!empty($_SESSION['imie_nazwisko']) && isset($_SESSION['imie_nazwisko'][0])) {
    echo strtoupper($_SESSION['imie_nazwisko'][0]);
} else {
    echo "U";
}
?></div>
                    <div class="u-text">
                        <h4><?php echo $_SESSION['imie_nazwisko']; ?></h4>
                        <p class="text-muted"><?php echo $_SESSION['username']; ?></p>
                    </div>
                </div>
            </li>
            <li>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="..\username.php">Zmiana nazwy użytkownika</a>
                <a class="dropdown-item" href="..\password.php">Zmiana hasła</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" id="darkModeButton" href="#">Tryb ciemny</a>
                <a class="dropdown-item btn-sidebar" href="#">Tryb paska bocznego</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="..\logout.php">Wyloguj się</a>
            </li>
        </div>
    </ul>
<?php } ?>
        </li>
    </ul>
</div>
</nav>
</div>
<script>
  $(document).ready(function(){
    $(".btn-sidebar").click(function(){
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