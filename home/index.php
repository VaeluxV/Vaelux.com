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
$directory = '/images/irl_trains';
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
            <p>I am aware of the issue with the banner being really buggy for chrome users</p>
            <br>
            <p>It is currently known to happen on all chromium based browsers. I am working on fixing this.</p>
            <br>
            <p> </p>
            <a href="/about-me" class="cta-button">More info about me</a>
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
<script>
document.addEventListener("DOMContentLoaded", () => {
    let index = 0;
    let currentLayer = 'A';
    const delayBetween = 10000;
    const getLayer = id => document.getElementById(`layer${id}`);
    const getVideo = id => getLayer(id).querySelector('video');

    function showMedia(mediaItem, targetLayer, callback) {
        const layer = getLayer(targetLayer);
        const video = getVideo(targetLayer);
        const isVideo = mediaItem.type === 'video';

        if (isVideo) {
            video.src = mediaItem.path;
            video.style.display = 'block';
            video.load();
            video.oncanplay = () => {
                video.play();
                layer.style.backgroundImage = '';
                callback();
            };
        } else {
            video.pause();
            video.style.display = 'none';
            video.src = '';
            layer.style.backgroundImage = `url('${mediaItem.path}')`;
            callback();
        }
    }

    function transitionToNext() {
        const nextLayer = currentLayer === 'A' ? 'B' : 'A';
        const current = getLayer(currentLayer);
        const next = getLayer(nextLayer);

        const mediaItem = media[index];
        index = (index + 1) % media.length;

        showMedia(mediaItem, nextLayer, () => {
            next.classList.add('visible');
            current.classList.remove('visible');
            currentLayer = nextLayer;
            setTimeout(transitionToNext, delayBetween);
        });
    }

    transitionToNext();
});
</script>
</body>
</html>
