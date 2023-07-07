<?php
require_once('../auth.php');
    if($_SESSION['role_parts']==1){
        $_SESSION['role_parts']=0;
    }
    else if($_SESSION['role_parts']==0){
        $_SESSION['role_parts']=1;

    }
    header("Location: main.php");
    exit();

?>