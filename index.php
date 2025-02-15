<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'projects', 'gallery', 'about', 'contact'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaelux.com</title>
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
