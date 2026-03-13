<?php
include "../methods.php";
include "../header.php";

if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

$user_id = $_SESSION['user_id'];

// Hämta användarens roll
$sql = "SELECT role FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $user_id]);
$user_role = $stmt->fetch(PDO::FETCH_ASSOC)['role'];

// Bara manager och admin får access
if ($user_role != 'manager' && $user_role != 'admin') {
    header("Location: ../browse/");
    exit;
}

// Hantera borttagning av användare (manager/admin)
if (isset($_POST['delete_user'])) {
    $delete_id = $_POST['user_id'];
    
    // Ta bort användarens kommentarer först
    $del_comments = "DELETE FROM comments WHERE user_id = :id OR target_id = :id";
    $stmt = $conn->prepare($del_comments);
    $stmt->execute([':id' => $delete_id]);
    
    // Ta bort användaren
    $del_user = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($del_user);
    $stmt->execute([':id' => $delete_id]);
    
    $_SESSION['success'] = "Användare borttagen!";
    header("Location: admin.php");
    exit;
}

// komentar moderering 
if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    
    $del = "DELETE FROM comments WHERE id = :id";
    $stmt = $conn->prepare($del);
    $stmt->execute([':id' => $comment_id]);
    
    $_SESSION['success'] = "Kommentar borttagen!";
    header("Location: admin.php");
    exit;
}

// fula ord borttagning
if (isset($_POST['moderate_comments'])) {
    $bad_words = ['fan', 'helvete', 'shit', 'fuck', 'fucker', 'fucking', 'fucked', 'fucked up', 'fucked up', 'nigga', 'nigger', 'retard', 'retarded', 
     'ass', 'asshole', 'bitch', 'bitches', 'boob', 'boobs', 'cunt', 'cunts', 'dick', 'dicks', 'dickhead', 'dickheads', 'dildo', 'dildos', 'dumbass',
     'autist', 'skit']; 
    
    foreach ($bad_words as $word) {
        $sql = "DELETE FROM comments WHERE comment LIKE :word";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':word' => '%' . $word . '%']);
    }
    
    $_SESSION['success'] = "Fula kommentarer borttagna!";
    header("Location: admin.php");
    exit;
}

// Hämta alla användare
$users_sql = "SELECT id, username, real_name, email, role FROM users ORDER BY id DESC";
$users = $conn->query($users_sql)->fetchAll(PDO::FETCH_ASSOC);

// Hämta alla kommentarer
$comments_sql = "SELECT c.*, u1.real_name as commenter, u2.real_name as target 
                 FROM comments c
                 JOIN users u1 ON c.user_id = u1.id
                 JOIN users u2 ON c.target_id = u2.id
                 ORDER BY c.created_at DESC";
$comments = $conn->query($comments_sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .tab-menu { margin-bottom: 20px; }
        .tab { padding: 10px 20px; background: #f0f0f0; border: none; cursor: pointer; }
        .tab.active { background: #007bff; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn { padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; }
        .success { background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <?php include "../nav.php" ?>
    
    <h1>Content Management</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <!-- Tab -->
    <div class="tab-menu">
        <button class="tab active" onclick="showTab('users')">Användare</button>
        <button class="tab" onclick="showTab('comments')">Kommentarer</button>
        <?php if ($user_role == 'admin'): ?>
            <button class="tab" onclick="showTab('moderate')">Auto-moderering</button>
        <?php endif; ?>
    </div>
    
    <!-- Användare tab -->
    <div id="users" class="tab-content active">
        <h2>Hantera användare</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Namn</th>
                <th>Username</th>
                <th>Email</th>
                <th>Roll</th>
                <th>Åtgärd</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['real_name']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                    <?php if ($user_role == 'admin' || ($user_role == 'manager' && $user['role'] == 'user')): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Ta bort användare?')">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger">Ta bort</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <!-- Kommentarer tab -->
    <div id="comments" class="tab-content">
        <h2>Hantera kommentarer</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Från</th>
                <th>Till</th>
                <th>Kommentar</th>
                <th>Skapad</th>
                <th>Åtgärd</th>
            </tr>
            <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?php echo $comment['id']; ?></td>
                <td><?php echo htmlspecialchars($comment['commenter']); ?></td>
                <td><?php echo htmlspecialchars($comment['target']); ?></td>
                <td><?php echo htmlspecialchars(substr($comment['comment'], 0, 50)) . '...'; ?></td>
                <td><?php echo $comment['created_at']; ?></td>
                <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Ta bort kommentar?')">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                        <button type="submit" name="delete_comment" class="btn btn-danger">Ta bort</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <!-- bort med alla fula ord -->
    <?php if ($user_role == 'admin'): ?>
    <div id="moderate" class="tab-content">
        <h2>Auto moderera bort alla dåliga ord</h2>
        <p>Ta bort alla kommentarer som innehåller fula ord:</p>
        <form method="POST">
            <button type="submit" name="moderate_comments" class="btn btn-warning" onclick="return confirm('Detta tar bort alla kommentarer med fula ord. Fortsätt?')">
                Kör moderering
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
function showTab(tabName) {
    // Dölj alla tabbar
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Visa vald tab
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}
</script>
</body>
</html>