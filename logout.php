<?php
require_once 'auth.php' ;
logUserActivity($_SESSION['imie_nazwisko'],'Wylogowanie');
logout();
header('Location: index.php');

exit();
?>