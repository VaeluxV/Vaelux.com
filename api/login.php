<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing email or password']);
    exit;
}

// Lookup user
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// Check if email is verified
if (!$user['email_verified']) {
    http_response_code(403);
    echo json_encode([
        'error' => 'Email not verified', 
        'message' => 'Please check your email and click the verification link before logging in.',
        'email' => $user['email']
    ]);
    exit;
}

session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

echo json_encode(['success' => true, 'username' => $user['username']]);
