<?php
require_once __DIR__ . '/../utils.php';

function createThumbnail($src, $dest, $width, $height, $quality = 40) {
    // Validate input file path
    if (!validate_file_path($src)) {
        return false;
    }
    
    // Validate destination path
    if (!validate_file_path(dirname($dest))) {
        return false;
    }
    
    // Check if source file exists and is readable
    if (!is_readable($src)) {
        return false;
    }
    
    $image_info = getimagesize($src);
    if ($image_info === false) {
        return false;
    }
    
    list($orig_width, $orig_height, $type) = $image_info;
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
    
    if (!$image) {
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
    if (!$thumb) {
        imagedestroy($image);
        return false;
    }

    // Resize and crop image
    $resized_image = imagecreatetruecolor($new_width, $new_height);
    if (!$resized_image) {
        imagedestroy($image);
        imagedestroy($thumb);
        return false;
    }
    
    imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
    imagecopy($thumb, $resized_image, 0, 0, $crop_x, $crop_y, $width, $height);

    // Save thumbnail as JPG with specified quality
    $result = imagejpeg($thumb, $dest, $quality);
    
    imagedestroy($image);
    imagedestroy($thumb);
    imagedestroy($resized_image);

    return $result;
}

// Set directories with validation
$document_root = get_server_var('DOCUMENT_ROOT');
if (empty($document_root) || !is_dir($document_root)) {
    die('Invalid server configuration');
}

$dir = $document_root . '/images/photo_library/';
$thumbDir = $document_root . '/images/photo_library/thumbnails/';

// Validate directories are within document root
if (!validate_file_path($dir) || !validate_file_path($thumbDir)) {
    die('Invalid directory configuration');
}

// Create thumbnails directory if they do not exist
if (!is_dir($thumbDir)) {
    if (!mkdir($thumbDir, 0755, true)) {
        die('Failed to create thumbnails directory');
    }
}

// Process all images
$images = glob($dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
if ($images !== false) {
    foreach ($images as $image) {
        // Validate image path
        if (!validate_file_path($image)) {
            continue;
        }
        
        $thumbPath = $thumbDir . basename($image);
        
        // Check if the thumbnail already exists
        if (!file_exists($thumbPath)) {
            createThumbnail($image, $thumbPath, 1280, 720);
        }
    }
}
?>
