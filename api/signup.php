<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

// Helper to generate unique UUID
function generateUniqueUUID($db) {
    do {
        $uuid = bin2hex(random_bytes(16));
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE uuid = ?");
        $stmt->execute([$uuid]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);
    return $uuid;
}

// Validate and sanitize inputs
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$displayName = trim($data['display_name'] ?? '');

// Basic checks
if (!$username || !$email || !$password || !$displayName) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Check if username or email already exists
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetchColumn() > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'Username or email already in use']);
    exit;
}

// Generate UUID and hash password
$uuid = generateUniqueUUID($db);
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $db->prepare("INSERT INTO users (uuid, username, email, password_hash, display_name) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$uuid, $username, $email, $passwordHash, $displayName]);

echo json_encode(['success' => true]);
