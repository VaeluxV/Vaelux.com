<?php
// Include shared security utilities
// Note: include_once is necessary for modular code organization and prevents function redeclaration
include_once __DIR__ . '/utils.php';



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
