<?php
// Determine the requested page
$page = $_GET['page'] ?? trim($_SERVER['REQUEST_URI'], '/');
$allowed_pages = ['home', 'projects', 'gallery', 'about', 'contact'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home'; // Default to home if invalid
}

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($is_ajax) {
    include "pages/$page.php";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Website</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div id="content">
        <?php include "pages/$page.php"; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/transitions.js"></script>
</body>
</html>
