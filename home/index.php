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
function server_var(string $key, $default = '') {
    return $_SERVER[$key] ?? $default;
}

include __DIR__ . '/../headers/main_header.php';
?>

<!-- Preload media -->
<?php
$directory = '/images/hero_imgs';
$mediaFiles = glob(server_var('DOCUMENT_ROOT') . $directory . '/*.{jpg,jpeg,png,gif,mp4,webm}', GLOB_BRACE);
sort($mediaFiles);

foreach ($mediaFiles as $file) {
    $filePath = $directory . '/' . basename($file);
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo '<img src="' . htmlspecialchars($filePath) . '" style="display: none;" alt="">';
    } elseif (in_array($ext, ['mp4', 'webm'])) {
        echo '<link rel="preload" as="video" href="' . htmlspecialchars($filePath) . '" type="video/' . $ext . '">';
    }
}
?>

<main>
    <section class="hero-container">
        <div class="hero hero-layer visible" id="layerA">
            <video class="hero-video" muted playsinline loop></video>
        </div>
        <div class="hero hero-layer" id="layerB">
            <video class="hero-video" muted playsinline loop></video>
        </div>

        <div class="hero-content">
            <h1>Welcome!</h1>
            <p></p>
        </div>
    </section>

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

<!-- Inject media list from PHP -->
<?php
echo "<script>
const media = [";
foreach ($mediaFiles as $file) {
    $mediaPath = str_replace(server_var('DOCUMENT_ROOT'), '', $file);
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    echo json_encode([
        'path' => $mediaPath,
        'type' => in_array($ext, ['mp4', 'webm']) ? 'video' : 'image'
    ]) . ',';
}
echo "];
</script>";
?>

<!-- Hero media transition logic -->
<script src="/js/hero_media.js"></script>
</body>
</html>
