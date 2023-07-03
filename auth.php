<?php
session_start();
function isLoggedIn()
{
    return isset($_SESSION['imie_nazwisko']);
}
function isUserMesser()
{
    return isset($_SESSION['username']) && $_SESSION['role_messer'] == 1;
}
function isUserParts()
{
    return isset($_SESSION['username']) && $_SESSION['role_parts'] == 1;
}

function logout()
{
    session_unset();
    session_destroy();
}

?>