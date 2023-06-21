<?php
require_once('auth.php');
logout();

header('Location: main.php');
exit();
?>