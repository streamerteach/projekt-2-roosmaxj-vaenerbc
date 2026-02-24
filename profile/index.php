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

    <h2>Edit your profile</h2>

    <form method="post" action="index.php">
    <input type="hidden" name="form" value="update">

    <label>Riktigt namn:</label><br>
    <input type="text" name="real_name" value="<?php echo $user['real_name']; ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>

    <label>Stad:</label><br>
    <input type="text" name="city" value="<?php echo $user['city']; ?>" required><br><br>

    <label>Annonstext:</label><br>
    <textarea name="ad_text" required><?php echo $user['ad_text']; ?></textarea><br><br>

    <label>Årslön:</label><br>
    <input type="number" name="salary" value="<?php echo $user['salary']; ?>" required><br><br>

    <label>Preferens:</label><br>
    <select name="preference">
        <option <?php if($user['preference']=="Man") echo "selected"; ?>>Man</option>
        <option <?php if($user['preference']=="Kvinna") echo "selected"; ?>>Kvinna</option>
        <option <?php if($user['preference']=="Båda") echo "selected"; ?>>Båda</option>
        <option <?php if($user['preference']=="Annat") echo "selected"; ?>>Annat</option>
        <option <?php if($user['preference']=="Alla") echo "selected"; ?>>Alla</option>
    </select><br><br>

    <input type="submit" value="Update Profile">
    </form>
    </section>

    <h2>Delete your profile</h2>

    <form method="post" action="index.php">
    <input type="hidden" name="form" value="delete">

    <label>Enter your password to confirm:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" style="background:red;color:white;">Delete Profile</button>
    </form>

    <?php include "../footer.php"?>
</div>
</body>
</html>