<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socials</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/socials_stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    // Safe function to get server variables with validation
    if (!function_exists('get_server_var')) {
        function get_server_var(string $key, $default = '') {
            return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
        }
    }

    include __DIR__ . '/../headers/main_header.php';
    ?>

    <main>
        <section class="socials">
            <h1>My socials</h1>
            <div class="social-buttons">
                <a href="https://github.com/VaeluxV" target="_blank" class="social-button"><i class="fab fa-github"></i> GitHub</a>
                <a href="https://discord.gg/XfJevQemgx" target="_blank" class="social-button"><i class="fab fa-discord"></i> Discord</a>
                <a href="https://www.instagram.com/vaelux_/" target="_blank" class="social-button"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="https://www.twitch.tv/vaelux_v" target="_blank" class="social-button"><i class="fab fa-twitch"></i> Twitch</a>
                <a href="https://www.youtube.com/@Vaelux_" target="_blank" class="social-button"><i class="fab fa-youtube"></i> YouTube</a>
                <a href="https://steamcommunity.com/id/vaelux/" target="_blank" class="social-button"><i class="fab fa-steam"></i> Steam</a>
                
                <!-- <a href="https://www.twitter.com" class="social-button"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="https://www.linkedin.com" class="social-button"><i class="fab fa-linkedin"></i> LinkedIn</a>
                <a href="https://www.facebook.com" class="social-button"><i class="fab fa-facebook"></i> Facebook</a> -->
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>
</html>
