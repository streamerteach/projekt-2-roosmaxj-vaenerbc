<?php include "../methods.php"?>
<?php

if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

if (empty($_POST['target_id']) || empty($_POST['vote'])) {
    header("Location: index.php");
    exit;
}

$user_id   = (int)$_SESSION['user_id'];
$target_id = (int)$_POST['target_id'];
$vote      = $_POST['vote'] === 'like' ? 'like' : 'dislike';

$sql = "INSERT INTO votes (user_id, target_id, vote)
        VALUES (:user_id, :target_id, :vote)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':user_id'   => $user_id,
    ':target_id' => $target_id,
    ':vote'      => $vote
]);

header("Location: index.php");
exit;