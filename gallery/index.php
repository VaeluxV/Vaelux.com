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
                function createThumbnail($src, $dest, $width, $height) {
                    list($orig_width, $orig_height, $type) = getimagesize($src);
                    $image = null;
                    
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            $image = imagecreatefromjpeg($src);
                            break;
                        case IMAGETYPE_PNG:
                            $image = imagecreatefrompng($src);
                            break;
                        case IMAGETYPE_GIF:
                            $image = imagecreatefromgif($src);
                            break;
                        default:
                            return false;
                    }
                    
                    $thumb = imagecreatetruecolor($width, $height);
                    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
                    
                    // Save thumbnail as JPG
                    imagejpeg($thumb, $dest, 90);
                    
                    imagedestroy($image);
                    imagedestroy($thumb);
                    return true;
                }

                // Set your directories
                $dir = $_SERVER['DOCUMENT_ROOT'] . '/images/photo_library/';
                $thumbDir = $_SERVER['DOCUMENT_ROOT'] . '/images/photo_library/thumbnails/';
                
                // Create thumbnails directory if it does not exist
                if (!file_exists($thumbDir)) {
                    mkdir($thumbDir, 0777, true);
                }
                
                // Retrieve all image files from the specified directory
                $images = glob($dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                foreach ($images as $image) {
                    $thumbPath = $thumbDir . basename($image);
                    
                    // Check if the thumbnail already exists, if not create it
                    if (!file_exists($thumbPath)) {
                        createThumbnail($image, $thumbPath, 1920, 1080);
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

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/footers/main_footer.php'; ?>
</body>
</html>
