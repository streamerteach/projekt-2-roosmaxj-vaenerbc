<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //starta en session för varje användare
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allt möjligt viktigt som vi använder ofta, sessionshantering, form validation etc.
// En funktion som tar bort whitespace, backslashes (escape char) och gör om < till html safe motsvarigheter
function test_input2($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Databaskonfiguration
$servername = "localhost";
include "db.php";

//Skapar en instans av POD klassen som vi kallar $conn
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connected to database";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

