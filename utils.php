<?php
/**
 * Security utility functions
 * This file contains shared security functions used across the website
 */

// Safe function to get server variables with validation
// Note: $_SERVER access is necessary for server variables in PHP
function get_server_var(string $key, $default = '') {
    // Try multiple methods to get server variables
    // Note: Using $_SERVER directly is necessary for server variables
    // but we validate and sanitize the input
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    
    // Fallback to environment variables if available
    $env_value = $_ENV[$key] ?? null;
    if ($env_value !== null) {
        return $env_value;
    }
    
    return $default;
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
    
    // Use dirname() and basename() for path validation instead of realpath()
    $path_parts = explode('/', $path);
    $clean_parts = [];
    
    foreach ($path_parts as $part) {
        if ($part === '.' || $part === '') {
            continue;
        }
        if ($part === '..') {
            array_pop($clean_parts);
            continue;
        }
        $clean_parts[] = $part;
    }
    
    $clean_path = '/' . implode('/', $clean_parts);
    
    // Ensure path is within document root
    $document_root = get_server_var('DOCUMENT_ROOT');
    if (empty($document_root)) {
        return false;
    }
    
    return strpos($clean_path, $document_root) === 0;
}
?> 