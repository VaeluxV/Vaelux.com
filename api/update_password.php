<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}
require_once __DIR__ . '/../includes/db.php';
$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
if (!$new_password || strlen($new_password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'New password must be at least 6 characters']);
    exit;
}
$stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !password_verify($current_password, $user['password_hash'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Current password is incorrect']);
    exit;
}
if (password_verify($new_password, $user['password_hash'])) {
    http_response_code(400);
    echo json_encode(['error' => 'New password must be different from the current password']);
    exit;
}
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
$stmt->execute([$new_hash, $user_id]);
echo json_encode(['success' => true]); 