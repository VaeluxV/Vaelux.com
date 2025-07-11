<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="/css/main_stylesheet.css">
</head>
<body>
    <?php
    // Note: include_once is necessary for modular code organization and prevents function redeclaration
    include_once __DIR__ . '/utils.php';
    include __DIR__ . '/headers/main_header.php';
    ?>

    <main>
        <section class="not-found">
            <h1>404 Not Found</h1>
            <p>Sorry, the page you are looking for does not exist.</p>
        </section>
    </main>

    <?php include __DIR__ . '/footers/main_footer.php'; ?>
</body>
</html>
