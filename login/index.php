<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php require_once '/home/v/vaenerbc/html/projekt-1-roosmaxj-vaenerbc/methods.php'; ?>


<?php include "../methods.php"?>
<?php include "../header.php" ?>

<body>
    <div id="conatiner"> <!-- max bredd 800px -->
    <?php include "../nav.php" ?>
    <section>
    <article>
        <h2>BrickGallery</h2>
        <p>Logga in eller registrera dig här!</p>
    </article>
    
    <h2>Register</h2>
    <form method="post" action="login.php">
    <input type="hidden" name="form" value="register">

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Riktigt namn:</label><br>
    <input type="text" name="real_name" required><br><br>

    <label>Lösenord:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Stad:</label><br>
    <input type="text" name="city" required><br><br>

    <label>Annonstext (“Berätta om dig”):</label><br>
    <textarea name="ad_text" required></textarea><br><br>

    <label>Årslön:</label><br>
    <input type="number" name="salary" required><br><br>

    <label>Preferens:</label><br>
    <select name="preference" required>
        <option value="Man">Man</option>
        <option value="Kvinna">Kvinna</option>
        <option value="Båda">Båda</option>
        <option value="Annat">Annat</option>
        <option value="Alla">Alla</option>
    </select><br><br>

    <input type="submit" value="Register">
    </form>


    <hr>


    <h2>Login</h2>
    <form method="post" action="login.php">
    <input type="hidden" name="form" value="login">

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" value="Login">
    </form>


    </article>
    <?php include "../footer.php"?>

</div>
</body>
</html>