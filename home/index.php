<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
</head>

<body>
<?php
    // Note: include_once is necessary for modular code organization and prevents function redeclaration
    include_once __DIR__ . '/../utils.php';
include __DIR__ . '/../headers/main_header.php';

$json_path = '/images/hero-banner/hero_banner_home.json';
?>

<main>
    <?php include __DIR__ . '/../components/hero_banner.php'; ?>
    <section class="features">
        <div class="feature">
            <h2 class="h2subhead">Website WIP</h2>
            <p>The website is still a work in progress.</p>
            <br>
            <h2 class="h2subhead">Photo gallery</h2>
            <a href="/gallery" class="cta-button">To the full photo gallery</a>
        </div>
        <div class="feature">
            <h2 class="h2subhead">About me</h2>
            <p>More info about me and my projects.</p>
            <br>
            <a href="/about-me" class="cta-button">More info about me</a>
        </div>
    </section>

    <section class="subsection">
        <div class="subsectiontext">
            <h2>Check back soon!</h2>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../footers/main_footer.php'; ?>

<!-- Hero media transition logic -->
<script src="/js/hero_banner.js"></script>
</body>
</html>
