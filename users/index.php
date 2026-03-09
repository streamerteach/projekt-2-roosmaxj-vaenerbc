<?php
include "../methods.php";
include "../header.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

$user_id = $_SESSION['user_id'];
$profile_id = isset($_GET['id']) ? $_GET['id'] : 0;

// FLYTTA KOMMENTARS-LOGIKEN HIT - ALLRA FÖRST EFTER INCLUDES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $target_id = $_POST['target_id'];
    $comment = trim($_POST['comment'] ?? '');
    
    if (!empty($comment)) {
        $sql = "INSERT INTO comments (user_id, target_id, comment) VALUES (:user_id, :target_id, :comment)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':target_id' => $target_id,
            ':comment' => $comment
        ]);
        $_SESSION['success'] = "Kommentar tillagd!";
    } else {
        $_SESSION['error'] = "Kommentaren får inte vara tom";
    }
    
    // OMDIRIGERING - detta måste ske innan någon HTML
    header("Location: index.php?id=" . $target_id);
    exit;
}

// Om inget id anges eller om det är ens eget id, gå tillbaka
if (!$profile_id || $profile_id == $user_id) {
    header("Location: ../browse/");
    exit;
}

// Hämta användarinformation
$sql = "SELECT id, real_name, salary, ad_text, profile_pic, username 
        FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $profile_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../browse/");
    exit;
}

// Hämta kommentarer till denna profil
$comment_sql = "SELECT 
                    c.*,
                    u.real_name as commenter_name,
                    u.profile_pic as commenter_pic
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.target_id = :target_id
                ORDER BY c.created_at DESC";
$comment_stmt = $conn->prepare($comment_sql);
$comment_stmt->execute([':target_id' => $profile_id]);
$comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['real_name']); ?>s profil</title>
    <link rel="stylesheet" href="./user_style.css">
</head>
<body>
<div class="container">
    <?php include "../nav.php" ?>
    
    <a href="../browse/" class="back-link">← Tillbaka till bläddra</a>
    
    <!-- Visa meddelanden -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <!-- Profilkort -->
    <div class="profile-card">
        <?php if (!empty($user['profile_pic'])): ?>
            <img src="../profile/pictures/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                 width="250" alt="Profilbild">
        <?php else: ?>
            <div class="no-image">Ingen bild</div>
        <?php endif; ?>
        
        <h1><?php echo htmlspecialchars($user['real_name']); ?></h1>
        <p><strong>Användarnamn:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Lön:</strong> <?php echo htmlspecialchars($user['salary']); ?> €</p>
        <p><?php echo nl2br(htmlspecialchars($user['ad_text'])); ?></p>
    </div>
    
    <!-- Kommentarsformulär -->
    <div class="comment-form">
        <h3>Skriv en kommentar till <?php echo htmlspecialchars($user['real_name']); ?></h3>
        <form method="POST" action="">
            <input type="hidden" name="add_comment" value="1">
            <input type="hidden" name="target_id" value="<?php echo $profile_id; ?>">
            <textarea name="comment" rows="4" placeholder="Skriv din kommentar här..." required></textarea>
            <button type="submit" class="btn">Skicka kommentar</button>
        </form>
    </div>
    
    <!-- Kommentarssektion -->
    <div class="comments-section">
        <h2>
            Kommentarer 
            <span class="comment-count"><?php echo count($comments); ?></span>
        </h2>
        
        <?php if (empty($comments)): ?>
            <p style="text-align: center; color: #999; padding: 40px;">
                Inga kommentarer än.
            </p>
        <?php endif; ?>
        
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment-header">
                    <div class="comment-avatar">
                        <?php if (!empty($comment['commenter_pic'])): ?>
                            <img src="../profile/pictures/<?php echo htmlspecialchars($comment['commenter_pic']); ?>" 
                                 alt="<?php echo htmlspecialchars($comment['commenter_name']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="comment-meta">
                        <span class="commenter-name">
                            <?php echo htmlspecialchars($comment['commenter_name']); ?>
                        </span>
                        <span class="comment-time">
                            <?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?>
                        </span>
                    </div>
                </div>
                <div class="comment-text">
                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php include "../footer.php"?>
</div>
</body>
</html>