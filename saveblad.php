<?php 
require_once("auth.php");
if (isset($_POST['errorMessage'])) {
    $errorMessage = $_POST['errorMessage'];
    $previousPage = $_SERVER['HTTP_REFERER'];
        logUserActivity($previousPage,$errorMessage);
}

?>