<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/about_me_stylesheet.css">
    <link rel="stylesheet" href="/css/socials_stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    // function server_var(string $key, $default = '') { // Function to get server variable(s) to prevent direct use of $_SERVER as much as possible
    //     return $_SERVER[$key] ?? $default;
    // }

    include __DIR__ . '/../headers/main_header.php';
    ?>

    <main>
        <div class="content-container">
            <section class="about-me">
                <h1>About Me</h1>
                <img src="/images/profiles/Eurostar-PBKA-square.jpg" alt="Placeholder Photo" class="about-me-photo">
                <h2>Vaelux</h2>
                <p>Hi, I'm Valerie, also known as "Vaelux" online!</p>
                <p>I am 21 years old, and have a wide range of hobbies.</p>
                <p>These hobbies include:</p>
                <ul>
                    <li>3D printing</li>
                    <li>Electronics</li>
                    <li>Gaming</li>
                    <li>Programming</li>
                    <li>Trains & model railroading</li>
                </ul>
                <p>And many more..</p>
            </section>

            <section class="socials">
                <h1>My Socials</h1>
                <div class="social-buttons">
                    <a href="https://github.com/VaeluxV" target="_blank" class="social-button"><i
                            class="fab fa-github"></i> GitHub</a>
                    <a href="https://discord.gg/XfJevQemgx" target="_blank" class="social-button"><i
                            class="fab fa-discord"></i> Discord</a>
                    <!-- <a href="https://www.instagram.com/vaelux_/" target="_blank" class="social-button"><i
                            class="fab fa-instagram"></i> Instagram</a> -->
                    <a href="https://www.twitch.tv/vaelux_v" target="_blank" class="social-button"><i
                            class="fab fa-twitch"></i> Twitch</a>
                    <a href="https://www.youtube.com/@Vaelux_" target="_blank" class="social-button"><i
                            class="fab fa-youtube"></i> YouTube</a>
                    <a href="https://steamcommunity.com/id/vaelux/" target="_blank" class="social-button"><i
                            class="fab fa-steam"></i> Steam</a>
                </div>
            </section>
        </div>
    </main>


    <?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>

</html>