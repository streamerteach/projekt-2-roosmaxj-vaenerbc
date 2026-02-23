<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //starta ne session för varje användare
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    // Funktion för input sanitation


if (!function_exists('test_input')) {
    function test_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}

