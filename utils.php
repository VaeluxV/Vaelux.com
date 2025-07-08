<?php
/**
 * Security utility functions
 * This file contains shared security functions used across the website
 */

// Safe function to get server variables with validation
function get_server_var(string $key, $default = '') {
    // Try multiple methods to get server variables
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }
    
    // Use filter_input as an alternative to direct $_SERVER access
    $value = filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
    if ($value !== null) {
        return $value;
    }
    
    // Final fallback to $_SERVER if other methods don't work
    return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
}

// Safe function to escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Safe function to validate and sanitize page parameter
function validate_page($page) {
    // Remove any dangerous characters and normalize
    $page = preg_replace('/[^a-zA-Z0-9\-_]/', '', $page);
    return $page;
}

// Safe function to validate and sanitize file paths
function validate_file_path($path) {
    // Remove any null bytes and normalize path
    $path = str_replace("\0", '', $path);
    $path = realpath($path);
    
    // Ensure path is within document root
    $document_root = get_server_var('DOCUMENT_ROOT');
    if (empty($document_root)) {
        return false;
    }
    
    return $path && strpos($path, $document_root) === 0;
}
?> 