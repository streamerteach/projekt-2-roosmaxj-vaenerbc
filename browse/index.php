<?php include "../methods.php"?>
<?php include "../header.php" ?>
<?php

// Bör va inloggad
if (empty($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

$user_id = $_SESSION['user_id'];

// fuctoner för sortering
$allowed_sorts = ['random', 'most_likes', 'most_dislikes', 'highest_salary'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'random';

// lazyloading
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

//  sortera beroende på vald 
switch($sort) {
    case 'most_likes':
        $order_by = "ORDER BY likes_count DESC, id";
        break;
    case 'most_dislikes':
        $order_by = "ORDER BY dislikes_count DESC, id";
        break;
    case 'highest_salary':
        $order_by = "ORDER BY salary DESC, id";
        break;
    case 'random':
    default:
        $order_by = "ORDER BY RAND()";
        break;
}

// Visa profiler baserat på likes och dislikes
// Lägg till comment_count i din SELECT
$sql = "SELECT 
            u.id, 
            u.real_name, 
            u.salary, 
            u.ad_text, 
            u.profile_pic,
            COUNT(CASE WHEN v.vote = 'like' THEN 1 END) as likes_count,
            COUNT(CASE WHEN v.vote = 'dislike' THEN 1 END) as dislikes_count,
            MAX(CASE WHEN v.user_id = :user_id THEN v.vote END) as user_vote,
            (SELECT COUNT(*) FROM comments c WHERE c.target_id = u.id) as comment_count
        FROM users u
        LEFT JOIN votes v ON u.id = v.target_id
        WHERE u.id != :user_id
        GROUP BY u.id
        {$order_by}
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kolla om det finns fler profiler
$check_sql = "SELECT COUNT(*) as total FROM users WHERE id != :user_id";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->execute([':user_id' => $user_id]);
$total_profiles = $check_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$has_more = ($offset + $limit) < $total_profiles;

// Om detta är en AJAX-förfrågan, skicka bara profilerna
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    if (empty($profiles)) {
        exit;
    }
    
    foreach ($profiles as $profile) {
        ?>
        <div class="card" data-profile-id="<?php echo $profile['id']; ?>">
            <?php if (!empty($profile['profile_pic'])): ?>
                <img src="../profile/pictures/<?php echo htmlspecialchars($profile['profile_pic']); ?>" 
                     width="250" alt="Profilbild">
            <?php else: ?>
                <div class="no-image">Ingen bild</div>
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($profile['real_name']); ?></h2>
            <p><strong>Lön:</strong> <?php echo htmlspecialchars($profile['salary']); ?> €</p>
            <p><?php echo nl2br(htmlspecialchars($profile['ad_text'])); ?></p>

            <!-- Likes och dislikes -->
            <div class="vote-stats">
                <span class="likes-count">❤️ <?php echo $profile['likes_count'] ?? 0; ?> gilla</span>
                <span class="dislikes-count">💔 <?php echo $profile['dislikes_count'] ?? 0; ?> ogilla</span>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <a href="../profile/?id=<?php echo $profile['id']; ?>" 
                   style="display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;">
                    💬 Kommentera (<?php echo $profile['comment_count'] ?? 0; ?>)
                </a>
            </div>
        </div>
        <?php
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Bläddra profiler</title>
    <link rel="stylesheet" href="./browse_style.css">
</head>
<body>
<div class="container">
    <?php include "../nav.php" ?>
    
    <h1>Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    
    <!-- Sorteringsmeny rad 14 och 13  -->
    <div class="sort-buttons">
        <a href="?sort=random" class="sort-btn <?php echo $sort == 'random' ? 'active' : ''; ?>">Slumpmässig</a>
        <a href="?sort=most_likes" class="sort-btn <?php echo $sort == 'most_likes' ? 'active' : ''; ?>">Mest gillade</a>
        <a href="?sort=most_dislikes" class="sort-btn <?php echo $sort == 'most_dislikes' ? 'active' : ''; ?>">Mest ogillade</a>
        <a href="?sort=highest_salary" class="sort-btn <?php echo $sort == 'highest_salary' ? 'active' : ''; ?>">Högsta lön</a>
    </div>
    
    <!-- Profil container -->
    <div id="profiles-container">
        <?php foreach ($profiles as $profile): ?>
            <div class="card" data-profile-id="<?php echo $profile['id']; ?>">
                <?php if (!empty($profile['profile_pic'])): ?>
                    <img src="../profile/pictures/<?php echo htmlspecialchars($profile['profile_pic']); ?>" 
                         width="250" alt="Profilbild">
                <?php else: ?>
                    <div class="no-image">Ingen bild</div>
                <?php endif; ?>
                
                <h2><?php echo htmlspecialchars($profile['real_name']); ?></h2>
                <p><strong>Lön:</strong> <?php echo htmlspecialchars($profile['salary']); ?> €</p>
                <p><?php echo nl2br(htmlspecialchars($profile['ad_text'])); ?></p>
                
                
                <div class="vote-stats">
                    <span class="likes-count">❤️ <?php echo $profile['likes_count'] ?? 0; ?> gillningar</span>
                    <span class="dislikes-count">💔 <?php echo $profile['dislikes_count'] ?? 0; ?> ogillningar</span>
                </div>
                
                <!-- Lägg till detta efter vote-stats eller i slutet av varje profil-kort -->
                <div style="margin-top: 15px; text-align: center;">
                    <a href="../users/?id=<?php echo $profile['id']; ?>" 
                    style="display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;">
                        💬 Kommentera (<?php echo $profile['comment_count'] ?? 0; ?>)
                    </a>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Ladda fler profiler knapp -->
    <div id="load-more-container">
        <button id="load-more" 
                onclick="loadMore()" 
                data-page="<?php echo $page; ?>" 
                data-sort="<?php echo $sort; ?>"
                <?php echo !$has_more ? 'disabled class="hidden"' : ''; ?>>
            Ladda fler profiler
        </button>
    </div>
    
    <!-- Laddar meddelande -->
    <div id="loading" class="<?php echo !$has_more ? 'hidden' : ''; ?>">
        Laddar fler profiler...
    </div>
    
    <!-- Ingen fler profiler meddelande -->
    <div id="no-more-profiles" class="<?php echo !$has_more && !empty($profiles) ? 'visible' : ''; ?>">
        <p>Inga fler profiler att visa</p>
    </div>
    
    <?php if (empty($profiles)): ?>
        <div class="end-message">
            <p>Inga profiler att visa just nu.</p>
        </div>
    <?php endif; ?>
</div>

<script>
// JavaScript för röstning och lazy loading
let isLoading = false;
let noMoreProfiles = <?php echo $has_more ? 'false' : 'true'; ?>;



function loadMore() {
    if (isLoading || noMoreProfiles) return;
    
    const button = document.getElementById('load-more');
    const currentPage = parseInt(button.dataset.page);
    const sort = button.dataset.sort;
    const nextPage = currentPage + 1;
    
    // Visa laddningsindikator
    isLoading = true;
    document.getElementById('loading').classList.add('visible');
    button.disabled = true;
    
    // Hämta nästa sida
    fetch(`?page=${nextPage}&sort=${sort}&ajax=1`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Nätverksfel');
            }
            return response.text();
        })
        .then(html => {
            // Dölj laddningsindikator
            document.getElementById('loading').classList.remove('visible');
            
            // Kolla om vi fick någon data
            if (html.trim().length === 0) {
                // Inga fler profiler
                noMoreProfiles = true;
                button.classList.add('hidden');
                document.getElementById('no-more-profiles').classList.add('visible');
            } else {
                // Lägg till nya profiler
                document.getElementById('profiles-container').insertAdjacentHTML('beforeend', html);
                
                // Uppdatera sidnumret
                button.dataset.page = nextPage;
                
                // Aktivera knappen igen
                button.disabled = false;
            }
            
            isLoading = false;
        })
        .catch(error => {
            console.error('Fel vid laddning:', error);
            document.getElementById('loading').classList.remove('visible');
            button.disabled = false;
            isLoading = false;
            
            // felmeddelande
            alert('Ett fel uppstod vid laddning av profiler. Försök igen.');
        });
}

// Lyssna på scroll för automatisk lazy loading
let scrollTimeout;
window.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    
    scrollTimeout = setTimeout(function() {
        const loadMoreBtn = document.getElementById('load-more');
        
        if (loadMoreBtn && !loadMoreBtn.disabled && !loadMoreBtn.classList.contains('hidden')) {
            const rect = loadMoreBtn.getBoundingClientRect();
            const isVisible = rect.top <= window.innerHeight + 200;
            
            if (isVisible && !isLoading && !noMoreProfiles) {
                loadMore();
            }
        }
    }, 100);
});

// Uppdatera noMoreProfiles när sidan laddas
document.addEventListener('DOMContentLoaded', function() {
    noMoreProfiles = <?php echo $has_more ? 'false' : 'true'; ?>;
});
</script>
</body>
</html>