<?php include "./methods.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrickGallery</title>
    <link rel="stylesheet" href="./styles.css">
    <script> src="../script.js"</script>
</head>

<body>
    <div id="conatiner"> <!-- max bredd 800px -->
        <header>
            <div id="logo">BrickGallery</div>
            <nav>
                <ul>
                    <li><a href="./home">Home</a></li>
                    <li><a href="./login">Login</a></li>
                </ul>
            </nav>
        </header>
        
    <section>
    <article> 
        <p>Choose language please</p>
        <br>
        <span class="language"><a href="./home/">English</a></span>
        <span class="language"><a href="./home/">Swedish</a></span>
        <br>
        <?php include "./cookie.php"?>
    </article>

    <article>
        <h2>Next building event!</h2>
        <br>
        <p>Countdown:</p>
        <?php include "./date.php"?> 

    </article>
    </section>
    <footer>
         <p>&copy; <?= date('Y') ?> BrickGallery - LEGO Showcase</p>
    </footer>
    </div>
</body>
</html>