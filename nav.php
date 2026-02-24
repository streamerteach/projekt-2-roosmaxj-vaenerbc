<header>
    <div id="logo">BrickGallery</div>
    <nav>
        <ul>
            <li><a href="/~roosmaxj/projekt-2-roosmaxj-vaenerbc/home">Home</a></li>

            <?php 
                if (!empty($_SESSION["username"])) {
                    echo '<li><a href="/~roosmaxj/projekt-2-roosmaxj-vaenerbc/profile">Profile</a></li>';
                } else {
                    echo '<li><a href="/~roosmaxj/projekt-2-roosmaxj-vaenerbc/login">Login</a></li>';
                }
            ?>
        </ul>
    </nav>
</header>

