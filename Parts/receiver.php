<?php
require_once '../auth.php'; 

if (isset($_GET['project_name'])) {
    // Pobranie wartości parametru i zapisanie jej w zmiennej sesyjnej
    $_SESSION['project_name'] = $_GET['project_name'];

    // Przekierowanie na inną stronę lub wykonanie innych działań w zależności od potrzeb
    header('Location: detale.php');
    exit;
} else {
    echo "Brak parametru 'project_name' w adresie URL";
}
?>