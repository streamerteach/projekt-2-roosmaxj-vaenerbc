<?php include "../methods.php"?>
<?php include "../header.php"?>
<?php

    if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
    }

    $user_id = $_SESSION['user_id'];

    // användarens info
    $sql = "SELECT id, real_name, salary, ad_text, profile_pic, username, email, city, preference
        FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // profilkomentarer
    $comment_sql = "SELECT
                    c.*,
                    u.real_name as commenter_name,
                    u.profile_pic as commenter_pic,
                    u.id as commenter_id
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.target_id = :target_id
                ORDER BY c.created_at DESC";
    $comment_stmt = $conn->prepare($comment_sql);
    $comment_stmt->execute([':target_id' => $user_id]);
    $comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);

    // formulerningupdatering
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form'])) {
    if ($_POST['form'] == 'update') {
        $real_name  = $_POST['real_name'];
        $email      = $_POST['email'];
        $city       = $_POST['city'];
        $ad_text    = $_POST['ad_text'];
        $salary     = $_POST['salary'];
        $preference = $_POST['preference'];

        $update_sql = "UPDATE users SET
                        real_name = :real_name,
                        email = :email,
                        city = :city,
                        ad_text = :ad_text,
                        salary = :salary,
                        preference = :preference
                      WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([
            ':real_name'  => $real_name,
            ':email'      => $email,
            ':city'       => $city,
            ':ad_text'    => $ad_text,
            ':salary'     => $salary,
            ':preference' => $preference,
            ':id'         => $user_id,
        ]);

        $_SESSION['success'] = "Profil uppdaterad!";
        header("Location: index.php");
        exit;
    }

    if ($_POST['form'] == 'delete') {
        // löenordkontroll
        $delete_sql  = "DELETE FROM users WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute([':id' => $user_id]);

        session_destroy();
        header("Location: ../login/");
        exit;
    }
    }
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Min profil</title>
    <link rel="stylesheet" href="./profile_style.css">
</head>
<body>
<div class="container">
    <?php include "../nav.php"?>


    <!-- Visa meddelanden -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success'];unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?php echo $_SESSION['error'];unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Profilkort -->
    <div class="profile-card">
        <?php if (! empty($user['profile_pic'])): ?>
            <img src="../profile/pictures/<?php echo htmlspecialchars($user['profile_pic']); ?>"
                 width="250" alt="Profilbild">
        <?php else: ?>
            <div class="no-image">Ingen bild</div>
        <?php endif; ?>

        <h1><?php echo htmlspecialchars($user['real_name']); ?></h1>
        <p><strong>Användarnamn:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Stad:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
        <p><strong>Lön:</strong> <?php echo htmlspecialchars($user['salary']); ?> €</p>
        <p><strong>Preferens:</strong> <?php echo htmlspecialchars($user['preference']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($user['ad_text'])); ?></p>
    </div>

    <?php
        // Hantera kommentar 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
            $target_id = $_POST['target_id'];
            $comment   = trim($_POST['comment'] ?? '');

            if (! empty($comment)) {
                $sql  = "INSERT INTO comments (user_id, target_id, comment) VALUES (:user_id, :target_id, :comment)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':user_id'   => $user_id,
                    ':target_id' => $target_id,
                    ':comment'   => $comment,
                ]);
                $_SESSION['success'] = "Kommentar tillagd!";
            } else {
                $_SESSION['error'] = "Kommentaren får inte vara tom";
            }

            // omladdning
            header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
            exit;
        }
    ?>
    <!-- Kommentarsformulär -->
    <div class="comment-form">
        <form method="POST" action="">
            <input type="hidden" name="add_comment" value="1">
            <input type="hidden" name="target_id" value="<?php echo $profile_id ?? $user_id; ?>">
            <textarea name="comment" rows="4" placeholder="Skriv din kommentar här..." required></textarea>
            <button type="submit" class="btn">Skicka kommentar</button>
        </form>
    </div>

    <!-- Kommentarssektion -->
    <div class="comments-section">
        <h2>
            Din vägg
            <span class="comment-count"><?php echo count($comments); ?></span>
        </h2>

        <?php if (empty($comments)): ?>
            <p style="text-align: center; color: #999; padding: 40px;">
                Du har inga kommentarer än. Skriv något själv eller vänta på att andra skriver!
            </p>
        <?php endif; ?>

        <?php foreach ($comments as $comment):
                $is_own_comment = ($comment['commenter_id'] == $user_id);
        ?>
            <div class="comment <?php echo $is_own_comment ? 'own-comment' : ''; ?>">
                <div class="comment-header">
                    <div class="comment-avatar">
                        <?php if (! empty($comment['commenter_pic'])): ?>
                            <img src="../profile/pictures/<?php echo htmlspecialchars($comment['commenter_pic']); ?>"
                                 alt="<?php echo htmlspecialchars($comment['commenter_name']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="comment-meta">
                        <span class="commenter-name">
                            <?php echo htmlspecialchars($comment['commenter_name']); ?>
                            <?php if ($is_own_comment): ?>
                                <span style="color: #999; font-size: 12px;">(du)</span>
                            <?php endif; ?>
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

    <hr>

    <!-- Redigeringssektion -->
    <div class="edit-section">
        <h2>Redigera din profil</h2>

        <form method="post" action="index.php">
            <input type="hidden" name="form" value="update">

            <label>Riktigt namn:</label>
            <input type="text" name="real_name" value="<?php echo htmlspecialchars($user['real_name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Stad:</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>

            <label>Annonstext:</label>
            <textarea name="ad_text" required><?php echo htmlspecialchars($user['ad_text']); ?></textarea>

            <label>Årslön (€):</label>
            <input type="number" name="salary" value="<?php echo htmlspecialchars($user['salary']); ?>" required>

            <label>Preferens:</label>
            <select name="preference">
                <option <?php if ($user['preference'] == "Man") {
                                echo "selected";
                        }
                        ?>>Man</option>
                <option <?php if ($user['preference'] == "Kvinna") {
                                echo "selected";
                        }
                        ?>>Kvinna</option>
                <option <?php if ($user['preference'] == "Båda") {
                                echo "selected";
                        }
                        ?>>Båda</option>
                <option <?php if ($user['preference'] == "Annat") {
                                echo "selected";
                        }
                        ?>>Annat</option>
                <option <?php if ($user['preference'] == "Alla") {
                                echo "selected";
                        }
                        ?>>Alla</option>
            </select>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn">Uppdatera profil</button>
            </div>
        </form>
    </div>

        
    <div style="margin-bottom: 20px; text-align: right;">
        <form method="post" action="../logout.php" style="display: inline;">
            <button type="submit" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Logga ut
            </button>
        </form>
    </div>

    <!-- Ta bort profil -->
    <div class="delete-section">
        <h2>Ta bort din profil</h2>
        <p style="color: #721c24;">Varning! Detta går inte att ångra.</p>

        <form method="post" action="index.php" onsubmit="return confirm('Är du helt säker? Detta tar bort din profil permanent!');">
            <input type="hidden" name="form" value="delete">

            <label>Ange ditt lösenord för att bekräfta:</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">

            <button type="submit" class="btn btn-danger">Ta bort min profil permanent</button>
        </form>
    </div>

    <?php if (isset($_SESSION['user_id'])):
            // Admin eller ej
            $check_role = "SELECT role FROM users WHERE id = :id";
            $stmt       = $conn->prepare($check_role);
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC)['role'];
    ?>

        <?php if ($role == 'manager' || $role == 'admin'): ?>
            <a class="admin-link" href="../profile/admin.php">Admin</a>
        <?php endif; ?>
    <?php endif; ?>
    <?php include "../footer.php"?>
</div>

</body>
</html>