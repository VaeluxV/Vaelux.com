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
$new_email = trim($_POST['new_email'] ?? '');
$current_password = $_POST['current_password_email'] ?? '';
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
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
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$new_email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already in use']);
    exit;
}
$stmt = $db->prepare('UPDATE users SET email = ? WHERE id = ?');
$stmt->execute([$new_email, $user_id]);
echo json_encode(['success' => true]); 