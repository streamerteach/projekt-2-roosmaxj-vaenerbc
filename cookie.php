<?php
require_once "methods.php"; //startar session här


//Besöksräknaren!
//Identifiera användaren
$userIdentifier = $_SESSION['username'] ?? $_SERVER['REMOTE_ADDR'];

//Filvägar
$counterFile = "./data/visits.txt";
$logFile = "./data/visit_log.txt";


// Om räknarfilen inte finns ännu (t.ex. första gången sidan körs)
if (!file_exists($counterFile)) {
     // Skapar filen och skriver in "0" som startvärde för besöksräknaren. 
    file_put_contents($counterFile, "0");
}
// Läser innehållet i filen (antalet besökare) och gör om det till ett heltal!
$visitCount = (int)file_get_contents($counterFile);

//Kollar om användaren redan har besökt sidan!
$uniqueCookie = "unique_visitor";

if (!isset($_COOKIE[$uniqueCookie])) {

    // Ny unik besökare då ökar räknaren
    $visitCount++;
    file_put_contents($counterFile, $visitCount);

    // Sätter cookien så personen inte räknas igen
    setcookie($uniqueCookie, "1", time() + (365 * 24 * 60 * 60), "/");

    // Loggar besöket
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "$timestamp - $userIdentifier\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
} 

//Landningssidans cookie system!
//Hämtar användarnamn
// $_SESSION['username'] kan bytas på serverns plats också då får vi profilen användarnamn!
$username = $_SERVER['REMOTE_USER'] ?? "Gäst";

//Cookie för första besöket
$firstVisitCookie = "first_visit";
$cookieLifetime = time() + (365 * 24 * 60 * 60); // 1 år

if (!isset($_COOKIE[$firstVisitCookie])) {
    // Första gången användaren är här
    $firstVisit = date("Y-m-d H:i:s");
    setcookie($firstVisitCookie, $firstVisit, $cookieLifetime, "/");
    $isReturningUser = false;
} else {
    // Återkommande användare
    $firstVisit = $_COOKIE[$firstVisitCookie];
    $isReturningUser = true;
}

// Cookie consent var användar kan välja vilka cookies hen accepterar
$consentCookie = "cookie_consent";

if (isset($_POST['accept_all'])) {
    setcookie($consentCookie, "all", $cookieLifetime, "/");
    $consent = "all";
} elseif (isset($_POST['functional_only'])) {
    setcookie($consentCookie, "functional", $cookieLifetime, "/");
    $consent = "functional";
} else {
    $consent = $_COOKIE[$consentCookie] ?? null;
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Landningssida</title>
</head>
<body>
<br>
<h1>Välkommen till BrickGallery</h1>
<br>
<p>Antal unika besökare på denna webbsida: <strong><?php echo $visitCount; ?></strong></p>

<br>
<p>Hej <strong><?php echo htmlspecialchars($username); ?></strong>!</p>
<?php if ($isReturningUser): ?>
    <p>Välkommen tillbaka! Du var här första gången: <strong><?php echo $firstVisit; ?></strong></p>
<?php else: ?>
    <p>Detta verkar vara ditt första besök. Kul att du hittade hit!</p>
<?php endif; ?>

<br>
<hr>
<br>

<h2>Serverinformation</h2>
<ul>
    <li>PHP-version: <strong><?php echo phpversion(); ?></strong></li>
</ul>

<br>
<hr>
<br>

<h2>Cookie-inställningar</h2>

<?php if (!$consent): ?>
    <p>Vi använder funktionella kakor för att sidan ska fungera. Vill du tillåta fler?</p>

    <form method="post">
        <button name="accept_all">Tillåt alla kakor</button>
        <button name="functional_only">Endast funktionella</button>
    </form>

    <?php elseif ($consent === "functional"): ?>
        <p>Du har valt att endast tillåta funktionella kakor.</p>

    <?php elseif ($consent === "all"): ?>
        <p>Du har tillåtit alla kakor. Tack!</p>
    <?php endif; ?>

</body>
</html>