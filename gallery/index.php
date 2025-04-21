<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo gallery</title>
    <link rel="icon" type="image/x-icon" href="/images/favicons/main/favicon.ico">
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/gallery_stylesheet.css">
</head>
<body>
    <?php
    function server_var(string $key, $default = '') { // Function to get server variable(s) to prevent direct use of $_SERVER as much as possible
        return $_SERVER[$key] ?? $default;
    }

    include __DIR__ . '/../headers/main_header.php';
    ?>

    <main>
        <h1 style="text-align: center; padding: 44px 0px 0px 0px">Photo gallery</h1>
        <p style="text-align: center; padding: 14px 28px 0px 28px">Hint: You can click on a photo to view a full-screen, high resolution version of it!</p>
        <section class="features">
            <?php
                include $_SERVER['DOCUMENT_ROOT'] . '/gallery/generate_thumbnails.php';
                
                // Retrieve all image files from the specified directory
                $images = glob($dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                foreach ($images as $image) {
                    $thumbPath = $thumbDir . basename($image);
                    
                    // Check if the thumbnail already exists, if not create it
                    if (!file_exists($thumbPath)) {
                        createThumbnail($image, $thumbPath, 1280, 720);
                    }
                    
                    // Convert server path to web-accessible path
                    $imagePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $image);
                    $thumbPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $thumbPath);
                    
                    echo '<div class="feature">';
                    echo '<a href="' . $imagePath . '" target="_blank" rel="noreferrer noopener">';
                    echo '<img src="' . $thumbPath . '" alt="Photo">';
                    echo '</a>';
                    echo '</div>';
                }
            ?>
        </section>

        <section class="subsection">
            <div class="subsectiontext">
                <h2 class="h2subhead">Thank you for visiting!</h2>
                <p>More photos coming soon!</p>
            </div>
        </section>
        
    </main>

    <?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>
</html>
