<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website - Projects</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
</head>

<body>
    <?php
    // Safe function to get server variables with validation
    if (!function_exists('get_server_var')) {
        function get_server_var(string $key, $default = '') {
            return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
        }
    }

    include __DIR__ . '/../../../headers/main_header.php';
    ?>

    <main>
        <section class="features">
            <div class="feature">
                <h2 class="h2subhead">Website</h2>
                <p>This website is one of my many projects.</p>
                <p>I like working with many different coding languages and experimenting with making my own things from scratch.</p>
                <br>
                <p>I will eventually add more info here.</p>
                <br>
                <a href="https://github.com/VaeluxV/Vaelux.com" target="_blank" rel="noopener noreferrer" class="cta-button">Go to the GitHub repo</a>
                <br><br>
                <a href="/projects" rel="noopener noreferrer" class="cta-button">Back to the projects page</a>
                <!-- <a href="/home" rel="noopener noreferrer" class="cta-button">Go to the homepage</a> -->
            </div>
        </section>

        <section class="subsection">
            <div class="subsectiontext">
                <h2>Check back soon!</h2>
            </div>
        </section>
    </main>
    <script src="/js/media_library.js"></script>
    <?php include __DIR__ . '/../../../footers/main_footer.php'; ?>
</body>

</html>