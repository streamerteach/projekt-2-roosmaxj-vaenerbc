<?php include "../methods.php"?>
<?php include "../header.php" ?>

<body>
    <div id="conatiner"> <!-- max bredd 800px -->
    <?php include "../nav.php" ?>
    <section>
    <article>
        <h2>BrickGallery profile</h2>
    </article>
    <article>
        <?php include "../profile.php"?>
    </article>
    <article>
        <form method="post" action="../logout.php">
            <button type="submit">Logout</button>
        </form>
    </article>
    </section>

    <?php include "../footer.php"?>
</div>
</body>
</html>