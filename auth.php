﻿<?php
session_start();
function logUserActivity($username, $operation) {
    $logFilePath = 'C:\xampp\htdocs\programs\Tarkonprograms\dziennik.log';
    $logMessage = "[" . date('Y-m-d H:i:s') . "],$username,$operation" . PHP_EOL;

    // Otwarcie pliku dziennika w trybie dołączania
    $file = fopen($logFilePath, 'a');

    // Zapisanie komunikatu do pliku dziennika
    fwrite($file, $logMessage);

    // Zamknięcie pliku dziennika
    fclose($file);
}

function isIdent(): int {
    return isset($_SESSION['ID']) ? (int) $_SESSION['ID'] : 0;}
function isSidebar(): int{
    return isset($_SESSION['sidebar']) ? (int) $_SESSION['sidebar'] : 0;}
function isLoggedIn()
{
    return isset($_SESSION['imie_nazwisko']);
}
function isUserMesser()
{
    return isset($_SESSION['username']) && $_SESSION['role_messer'] == 1;
}

function isUserCutlogic(){
    return isset($_SESSION['username']) && $_SESSION['role_cutlogic'] == 1;
}

function isUserAdmin()
{
    return isset($_SESSION['username']) && $_SESSION['role_admin'] == 1;
}
function isUserParts()
{
    return isset($_SESSION['username']) && $_SESSION['role_parts'] == 1;
}
function isUserPartsKier()
{
    return isset($_SESSION['username']) && $_SESSION['role_parts_kier'] == 1;
}

function logout()
{
    session_unset();
    session_destroy();
}

?>