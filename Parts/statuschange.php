<?php
require_once('../auth.php');
    if($_SESSION['role_parts']==1){
        $_SESSION['role_parts']=0;
    }
    else if($_SESSION['role_parts']==0){
        $_SESSION['role_parts']=1;

    }
    $previousPage = $_SERVER['HTTP_REFERER'];
    header("Location: $previousPage");
    exit();

?>