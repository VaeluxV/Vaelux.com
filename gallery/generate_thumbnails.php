<?php
function createThumbnail($src, $dest, $width, $height, $quality = 50) {
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

    // Calculate aspect ratios
    $orig_aspect = $orig_width / $orig_height;
    $thumb_aspect = $width / $height;

    if ($orig_aspect >= $thumb_aspect) {
        // Original image is wider or the same aspect ratio as thumbnail
        $new_height = $height;
        $new_width = (int)($height * $orig_aspect);
    } else {
        // Original image is taller or the same aspect ratio as thumbnail
        $new_width = $width;
        $new_height = (int)($width / $orig_aspect);
    }

    // Calculate cropping area
    $crop_x = (int)(($new_width - $width) / 2);
    $crop_y = (int)(($new_height - $height) / 2);

    // Create a new true color image
    $thumb = imagecreatetruecolor($width, $height);

    // Resize and crop image
    $resized_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
    imagecopy($thumb, $resized_image, 0, 0, $crop_x, $crop_y, $width, $height);

    // Save thumbnail as JPG with specified quality
    imagejpeg($thumb, $dest, $quality);
    
    imagedestroy($image);
    imagedestroy($thumb);
    imagedestroy($resized_image);

    return true;
}

// Set your directories
$dir = $_SERVER['DOCUMENT_ROOT'] . '/images/photo_library/';
$thumbDir = $_SERVER['DOCUMENT_ROOT'] . '/images/photo_library/thumbnails/';

// Create thumbnails directory if it does not exist
if (!file_exists($thumbDir)) {
    mkdir($thumbDir, 0777, true);
}

// Process all images
$images = glob($dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
foreach ($images as $image) {
    $thumbPath = $thumbDir . basename($image);
    
    // Check if the thumbnail already exists
    if (!file_exists($thumbPath)) {
        createThumbnail($image, $thumbPath, 1920, 1080);
    }
}
?>
