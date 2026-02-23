<?php
date_default_timezone_set("Europe/Helsinki");

$error = null;
$targetTimestamp = null;
$actionTaken = false;

// POST: uppdate cookie 
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Set / Change timer
    if (isset($_POST["set-timer"])) {
        $actionTaken = true;
        $input = $_POST["date"] ?? "";

        if (!$input) {
            $error = "Give a date.";
        } else {
            $ts = strtotime($input);

            if ($ts === false) {
                $error = "Invalid date.";
            } elseif ($ts <= time()) {
                $error = "Date has to be in the future";
            } else {
                setcookie("targetTimestamp", $ts, time() + 30*24*60*60, "/");
                $targetTimestamp = $ts;
            }
        }
    }

    // Null the timer
    if (isset($_POST["reset-timer"])) {
        $actionTaken = true;
        setcookie("targetTimestamp", "", time() - 3600, "/");
        $targetTimestamp = null;
    }
}

// GET / REFRESH: Read cookie ONLy if POST happened
if (!$actionTaken && isset($_COOKIE["targetTimestamp"])) {
    $targetTimestamp = (int)$_COOKIE["targetTimestamp"];
}
?>
