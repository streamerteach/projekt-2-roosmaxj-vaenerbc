<?php include "../methods.php"?>
<?php include "../header.php" ?>
<?php

// Måste vara inloggad
if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

$user_id = $_SESSION['user_id'];

// Hämta en slumpmässig profil som inte är du själv
$sql = "SELECT id, real_name, salary, ad_text, profile_pic
        FROM users
        WHERE id != :id
        ORDER BY RAND()
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Bläddra profiler</title>
    <style>
        .card {
            width: 320px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .card img {
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .like, .dislike {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .like { background: #4CAF50; color: #fff; }
        .dislike { background: #f44336; color: #fff; }
    </style>
</head>
<body>

<div id="conatiner"> <!-- max bredd 800px -->
<?php include "../nav.php" ?>
<h1>Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

<?php if ($profile): ?>
    <div class="card">

        <?php if (!empty($profile['profile_pic'])): ?>
            <img src="../profile/pictures/<?php echo htmlspecialchars($profile['profile_pic']); ?>" 
                 width="250" alt="Profilbild">
        <?php else: ?>
            <div style="width:250px;height:250px;background:#eee;border-radius:10px;margin:0 auto 10px;">
                Ingen bild
            </div>
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($profile['real_name']); ?></h2>
        <p><strong>Lön:</strong> <?php echo htmlspecialchars($profile['salary']); ?> €</p>
        <p><?php echo nl2br(htmlspecialchars($profile['ad_text'])); ?></p>

        <form method="post" action="vote.php">
            <input type="hidden" name="target_id" value="<?php echo (int)$profile['id']; ?>">
            <button type="submit" name="vote" value="like" class="like">❤️ Gilla</button>
            <button type="submit" name="vote" value="dislike" class="dislike">💔 Ogilla</button>
        </form>

    </div>
<?php else: ?>
    <p>Inga fler profiler att visa just nu.</p>
<?php endif; ?>
</div>
</body>
</html>