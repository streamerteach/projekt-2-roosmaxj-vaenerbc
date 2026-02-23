<?php
// Include the file that starts the session
require_once __DIR__ . "/methods.php"; // 

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ./home/index.php");
exit;
