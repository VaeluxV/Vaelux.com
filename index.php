<?php
// Safe function to get server variables with validation
function get_server_var(string $key, $default = '') {
    return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
}

// Safe function to validate and sanitize page parameter
function validate_page($page) {
    // Remove any dangerous characters and normalize
    $page = preg_replace('/[^a-zA-Z0-9\-_]/', '', $page);
    return $page;
}

$request_uri = get_server_var('REQUEST_URI', '');
$page = validate_page(trim($request_uri, '/'));

// Define allowed pages to prevent directory traversal
$allowed_pages = ['', 'home', 'about-me', 'socials', 'privacy-policy'];

if (in_array($page, $allowed_pages)) {
    if ($page == '' || $page == 'home') {
        include __DIR__ . '/home/index.php';
    } elseif ($page == 'about-me') {
        include __DIR__ . '/about-me/index.php';
    } elseif ($page == 'socials') {
        include __DIR__ . '/socials/index.php';
    } elseif ($page == 'privacy-policy') {
        include __DIR__ . '/privacy-policy/index.php';
    }
} else {
    include __DIR__ . '/404.php';
}
?>
