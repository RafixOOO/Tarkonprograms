<?php
// Pobierz adres IP z nagłówka X-Forwarded-For, jeśli dostępny
$ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

// Wyświetl adres IP
echo "Twój adres IP: " . $ip_address;
?>
