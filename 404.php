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
    // Safe function to get server variables with validation
    if (!function_exists('get_server_var')) {
        function get_server_var(string $key, $default = '') {
            return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
        }
    }

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
