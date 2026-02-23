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
    
    <!-- REGISTER FORM -->
     <article>
        <h2>Register</h2>
        <form method="post">
            <input type="hidden" name="form" value="register">
            Username: <input type="text" name="username" required><br>
            Email: <input type="email" name="email" required><br>
            <input type="submit" value="Register">
        </form>
    </article>
    <?php include "./login.php" ?>
    <!-- LOGIN FORM -->
     <article>
        <h2>Login</h2>
        <form method="post">
            <input type="hidden" name="form" value="login">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
    </article>


    </article>
    <?php include "../footer.php"?>

</div>
</body>
</html>