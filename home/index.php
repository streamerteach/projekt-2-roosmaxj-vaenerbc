<?php include "../methods.php"?>
<?php include "../header.php" ?>

<body>
    <div id="conatiner"> <!-- max bredd 800px -->
    <?php include "../nav.php" ?>
    <section>

    <article class="hero-layout">
    <div class="hero-text">
        <h1>Welcome to BrickGallery</h1>
        <br>
        <p>
            BrickGallery is a community-driven LEGO showcase where builders of all skill levels 
            can upload their creations, explore others’ builds, and get inspired. Whether you’re 
            into detailed dioramas, custom minifigures, or experimental designs, this is your space 
            to share your imagination.
        </p>
        <br>
        <p>
            Create an account to start uploading your own builds, leave comments, and join the 
            growing community of LEGO enthusiasts.
        </p>
    </div>

    <div class="hero-image-wrapper">
        <img src="../assets/BG.png" alt="BrickGallery LEGO heads" class="hero-side-image">
    </div>
    </article>


    <article>
        <h2>How It Works</h2>
        <ul style="display: block; padding-left: 20px; line-height: 1.6;">
            <li><strong>Browse Builds:</strong> Explore creations uploaded by other users.</li>
            <li><strong>Create an Account:</strong> Register to unlock upload and interaction features.</li>
            <li><strong>Upload Your Creations:</strong> Share your LEGO builds with the world.</li>
            <li><strong>Like & Comment:</strong> Engage with the community and give feedback.</li>
            <li><strong>Join Challenges:</strong> Participate in themed building events.</li>
        </ul>
    </article>

    <article>
        <h2>Upcoming Event</h2>
        <p>
            A countdown to our next LEGO building challenge. Stay tuned and 
            prepare your bricks!
        </p>
        <br>
        <?php include "../date.php"?>
    </article>
    
    <!-- Countdown timer -->
    <article class="timer-article">
        <p>When will your next build be ready?</p>
        <form method="post">
            <input type="datetime-local" name="date" required>
            <button type="submit" name="set-timer">Start / Change</button>
            <button type="submit" name="reset-timer">Null</button>
            <?php include "../timer.php" ?>
        </form>

        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <p id="countdown"></p>
        
        <!-- PHP ti js filen -->
        <script>
            window.targetTimestampSeconds =
                <?= $targetTimestamp !== null ? $targetTimestamp : "null" ?>;
        </script>

        <script src="../countdown.js"></script>
    </article>

    <article class="comments-article">
        <?php include "../comments.php"; ?>
    </article>

 </section>
    
    <?php include "../footer.php"?>
    

</div>
</body>
</html>