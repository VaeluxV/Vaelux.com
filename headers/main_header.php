<header>
    <div class="logo">
        <a href="/home">Vaelux.com</a>
    </div>
    <nav>
        <ul class="header">
            <li><a href="/home">Home</a></li>
            <li><a href="/about-me">About Me</a></li>
            <li><a href="/projects">My Projects</a></li>
            <li><a href="/media-library">Media Library</a></li>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['user_id'])) {
                // Fetch user profile picture
                require_once __DIR__ . '/../includes/db.php';
                $stmt = $db->prepare('SELECT profile_picture FROM users WHERE id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $header_user = $stmt->fetch(PDO::FETCH_ASSOC);
                $profile_pic = $header_user && !empty($header_user['profile_picture']) ? htmlspecialchars($header_user['profile_picture']) : '/images/user_content/pre-made/profile_pictures/placeholder.jpg';
                echo '<li><a href="/dashboard">Dashboard</a></li>';
                echo '<li><a href="#"><img src="' . $profile_pic . '" alt="Profile" class="header-profile-pic"></a></li>';
            } else {
                echo '<li><a href="/auth/">Login / Sign Up</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>
<link rel="stylesheet" href="/css/main_stylesheet_header.css">
