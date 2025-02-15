<header>
    <nav>
        <ul>
            <?php
            $nav_links = [
                '/?page=home' => 'Home',
                '/?page=projects' => 'My Projects',
                '/?page=gallery' => 'Photo & Video Gallery',
                '/?page=about' => 'About Me',
                '/?page=contact' => 'Contact'
            ];

            foreach ($nav_links as $link => $name) {
                echo "<li><a href='$link' class='nav-link'>$name</a></li>";
            }
            ?>
        </ul>
    </nav>
</header>
