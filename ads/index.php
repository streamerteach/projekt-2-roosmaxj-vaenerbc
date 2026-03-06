<?php include "../methods.php"?>
<?php include "../header.php" ?>
<?php
$limit = 5; // antal profiler per sida
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // default sida 1
$offset = ($page - 1) * $limit; // hur många profiler vi hoppar över
$preference = $_GET['preference'] ?? null; // t.ex. ?preference=Man

$sort_by = $_GET['sort_by'] ?? 'salary'; // 'salary' eller 'likes'
$order = $_GET['order'] ?? 'DESC';       // 'DESC' eller 'ASC'
$allowed_sort = ['salary', 'likes'];
if (!in_array($sort_by, $allowed_sort)) $sort_by = 'salary';

// Måste vara inloggad
if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

$user_id = $_SESSION['user_id'];

$where = "u.id != :id";
$params = [':id' => $user_id];

if ($preference) {
    $where .= " AND u.preference = :pref";
    $params[':pref'] = $preference;
}

$sql = "SELECT u.id, u.real_name, u.salary, u.ad_text, u.profile_pic,
               (SELECT COUNT(*) FROM votes v WHERE v.target_id = u.id AND v.vote='like') AS likes
        FROM users u
        WHERE $where
        ORDER BY $sort_by $order
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(isset($_GET['ajax']) && $_GET['ajax']==1){
    foreach($profiles as $profile){
        // echo samma HTML som i foreach-loopen
        ?>
        <div class="card">
            <?php if (!empty($profile['profile_pic'])): ?>
                <img src="../profile/pictures/<?= htmlspecialchars($profile['profile_pic']) ?>" width="250">
            <?php else: ?>
                <div style="width:250px;height:250px;background:#eee;margin:0 auto 10px;"></div>
            <?php endif; ?>
            <h2><?= htmlspecialchars($profile['real_name']) ?></h2>
            <p>Lön: <?= htmlspecialchars($profile['salary']) ?> €</p>
            <p>Likes: <?= $profile['likes'] ?></p>
            <p><?= nl2br(htmlspecialchars($profile['ad_text'])) ?></p>
            <form method="post" action="vote.php">
                <input type="hidden" name="target_id" value="<?= (int)$profile['id'] ?>">
                <button type="submit" name="vote" value="like" class="like">❤️ Gilla</button>
                <button type="submit" name="vote" value="dislike" class="dislike">💔 Ogilla</button>
            </form>
        </div>
        <?php
    }
    exit; // sluta exekvera resten av sidan
}

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

<div id="container"> <!-- max bredd 800px -->
<?php include "../nav.php" ?>
<h1>Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
<div style="text-align:center;margin-bottom:20px;">
    <a href="?sort_by=salary&order=DESC">Lön högst först</a> |
    <a href="?sort_by=salary&order=ASC">Lön lägst först</a> |
    <a href="?sort_by=likes&order=DESC">Mest gillade först</a> |
    <a href="?sort_by=likes&order=ASC">Minst gillade först</a>
</div>
<div style="text-align:center;margin-bottom:20px;">
    <form method="get">
        <label>Filtrera på preferens:</label>
        <select name="preference">
            <option value="">Alla</option>
            <option value="Man" <?= ($preference=="Man")?"selected":"" ?>>Man</option>
            <option value="Kvinna" <?= ($preference=="Kvinna")?"selected":"" ?>>Kvinna</option>
            <option value="Båda" <?= ($preference=="Båda")?"selected":"" ?>>Båda</option>
            <option value="Annat" <?= ($preference=="Annat")?"selected":"" ?>>Annat</option>
            <option value="Alla" <?= ($preference=="Alla")?"selected":"" ?>>Alla</option>
        </select>
        <input type="hidden" name="sort_by" value="<?= $sort_by ?>">
        <input type="hidden" name="order" value="<?= $order ?>">
        <button type="submit">Filtrera</button>
    </form>
</div>

<?php if (!empty($profiles)): ?>
<div id="profiles-container">
    <?php foreach($profiles as $profile): ?>
        <div class="card">

            <?php if (!empty($profile['profile_pic'])): ?>
                <img src="../profile/pictures/<?= htmlspecialchars($profile['profile_pic']) ?>" 
                     width="250" alt="Profilbild">
            <?php else: ?>
                <div style="width:250px;height:250px;background:#eee;border-radius:10px;margin:0 auto 10px;">
                    Ingen bild
                </div>
            <?php endif; ?>

            <h2><?= htmlspecialchars($profile['real_name']) ?></h2>
            <p><strong>Lön:</strong> <?= htmlspecialchars($profile['salary']) ?> €</p>
            <p><strong>Likes:</strong> <?= $profile['likes'] ?></p>
            <p><?= nl2br(htmlspecialchars($profile['ad_text'])) ?></p>

            <form method="post" action="vote.php">
                <input type="hidden" name="target_id" value="<?= (int)$profile['id'] ?>">
                <button type="submit" name="vote" value="like" class="like">❤️ Gilla</button>
                <button type="submit" name="vote" value="dislike" class="dislike">💔 Ogilla</button>
            </form>

        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p>Inga fler profiler att visa just nu.</p>
<?php endif; ?>
</div>
<script>
let page = <?= $page ?>;
const limit = <?= $limit ?>;
const sort_by = "<?= $sort_by ?>";
const order = "<?= $order ?>";
const preference = "<?= $preference ?>";

window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
        // När användaren scrollar nära botten
        loadMoreProfiles();
    }
});

let loading = false;
function loadMoreProfiles() {
    if (loading) return;
    loading = true;
    page++;
    fetch(`index.php?page=${page}&limit=${limit}&sort_by=${sort_by}&order=${order}&preference=${preference}&ajax=1`)
        .then(res => res.text())
        .then(html => {
            if(html.trim() !== "") {
                document.getElementById('profiles-container').insertAdjacentHTML('beforeend', html);
                loading = false;
            }
        });
}
</script>
</body>
</html>