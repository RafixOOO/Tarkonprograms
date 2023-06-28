<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['username']);
}

function logout()
{
    session_unset();
    session_destroy();
}
?>