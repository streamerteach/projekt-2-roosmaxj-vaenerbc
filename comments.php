<?php
// comments.php
// Kräver session från cookie.php / methods.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$commentFile = __DIR__ . "/data/comments.txt";

// Se till att filen finns
if (!file_exists($commentFile)) {
    file_put_contents($commentFile, "");
}

$isLoggedIn = isset($_SESSION['username']);

// Hantera POST
if ($isLoggedIn && isset($_POST['submit_comment'])) {
    $text = trim($_POST['comment']);

    if ($text !== "") {
        $user = htmlspecialchars($_SESSION['username']);
        $text = htmlspecialchars($text);
        $time = date("Y-m-d H:i:s");

        file_put_contents(
            $commentFile,
            "$time|$user|$text\n",
            FILE_APPEND
        );
    }
}
?>

<hr>
<h2>Leave a comment!</h2>

<?php if ($isLoggedIn): ?>
    <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>

    <form method="post">
        <textarea name="comment" rows="4" cols="50" required></textarea><br><br>
        <button type="submit" name="submit_comment">Sends comment</button>
    </form>
<?php else: ?>
    <p><em>You must be logged in to leave comment!</em></p>
<?php endif; ?>

<h3>Kommentarer</h3>

<?php
$lines = file($commentFile, FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
    echo "<p>Inga kommentarer ännu.</p>";
} else {
    foreach (array_reverse($lines) as $line) {
        [$time, $user, $text] = explode("|", $line);
        echo "<p><strong>$user</strong> ($time)<br>$text</p><hr>";
    }
}
?>
