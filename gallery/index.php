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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/headers/main_header.php'; ?>

    <main>
        <h1 style="text-align: center; padding: 45px 0px 0px 0px">Photo gallery</h1>
        <section class="features">
            <?php
                // Directory where photos are stored
                $dir = $_SERVER['DOCUMENT_ROOT'] . '/images/photo_library/'; 
                
                // Retrieve all image files from the specified directory
                $images = glob($dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

                // Loop through each image and create a feature div with the image
                foreach($images as $image) {
                    // Convert server path to web-accessible path
                    $imagePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $image);
                    echo '<div class="feature">';
                    echo '<a href="' . $imagePath . '" target="_blank" rel="noreferrer noopener"><img src="' . $imagePath . '" alt="Photo"></a>';
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

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/footers/main_footer.php'; ?>
</body>
</html>