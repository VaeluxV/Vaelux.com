<?php
// Usage: include __DIR__ . '/../components/hero_banner.php';
// If $json_path is not set, fallback to the home page banner JSON.
// Security: Only allow JSON files from /images/hero-banner/
if (!isset($json_path)) {
    $json_path = '/images/hero-banner/hero_banner_home.json';
}
if (strpos($json_path, '/images/hero-banner/') !== 0) {
    // If $json_path is not in the allowed directory, reset to default
    $json_path = '/images/hero-banner/hero_banner_home.json';
}
?>
<link rel="stylesheet" href="/css/hero_banner.css">
<section class="hero-container" id="heroBanner" data-json="<?php echo htmlspecialchars($json_path); ?>">
    <div class="hero hero-layer visible" id="layerA"></div>
    <div class="hero hero-layer" id="layerB"></div>
    <div class="hero-content">
        <!-- Room for optional static content -->
    </div>
    <div class="hero-controls">
        <!-- JS will inject dots here -->
    </div>
    <button class="hero-arrow hero-arrow-left" aria-label="Previous" style="display:none;"></button>
    <button class="hero-arrow hero-arrow-right" aria-label="Next" style="display:none;"></button>
</section> 