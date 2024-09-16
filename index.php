<?php
$page = trim($_SERVER['REQUEST_URI'], '/');

if ($page == '' || $page == 'home') {
    include 'home/index.php';
} elseif ($page == 'about-me') {
    include 'about-me/index.php';
} elseif ($page == 'socials') {
    include 'socials/index.php';
} elseif ($page == 'privacy-policy') {
    include 'privacy-policy/index.php';
} else {
    include '404.php';
}
?>
