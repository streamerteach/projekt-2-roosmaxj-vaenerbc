<header>
    <div id="logo">BrickGallery</div>
    <nav>
        <ul>
            <li><a href="/~vaenerbc/projekt-2-roosmaxj-vaenerbc/home">Home</a></li>
            
            <li><a href="/~vaenerbc/projekt-2-roosmaxj-vaenerbc/ads">Builds</a></li>

            <li><a href="/~vaenerbc/projekt-2-roosmaxj-vaenerbc/browse">Browse</a></li>

            <?php 
                if (!empty($_SESSION["username"])) {
                    echo '<li><a href="/~vaenerbc/projekt-2-roosmaxj-vaenerbc/profile">Profile</a></li>';
                } else {
                    echo '<li><a href="/~vaenerbc/projekt-2-roosmaxj-vaenerbc/login">Login</a></li>';
                }
            ?>
        </ul>
    </nav>
</header>

