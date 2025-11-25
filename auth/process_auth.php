<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/filters.php';
require_once __DIR__ . '/../config/hcaptcha_keys.php';

function validate_hcaptcha($token) {
    global $hcaptcha_secretkey;
    if (!$token) return false;

    $data = [
        'secret' => $hcaptcha_secretkey,
        'response' => $token
    ];
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];
    $response = file_get_contents('https://hcaptcha.com/siteverify', false, stream_context_create($opts));
    $result = json_decode($response, true);
    return $result['success'] ?? false;
}

function generate_uuid() {
    return bin2hex(random_bytes(16));
}

// Validate hCaptcha
if (!validate_hcaptcha($_POST['h-captcha-response'] ?? '')) {
    http_response_code(400);
    exit('Invalid hCaptcha');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$type = $_POST['confirm_password'] ? 'signup' : ($_POST['username'] ? 'login' : 'reset');

// Sanitize input
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email.');
}

if ($type === 'signup') {
    $username = trim($_POST['username'] ?? '');
    $displayName = trim($_POST['display_name'] ?? '');
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($username) < 3 || strlen($displayName) < 3) {
        exit('Username or display name too short.');
    }

    if ($password !== $confirm) {
        exit('Passwords do not match.');
    }

    if (is_prohibited($username) || is_prohibited($displayName)) {
        exit('Inappropriate content detected.');
    }

    // Check if username or email exists
    $stmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        exit('Username or email already in use.');
    }

    // Insert user
    $uuid = generate_uuid();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (uuid, username, email, password_hash, display_name) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$uuid, $username, $email, $hash, $displayName]);

    echo 'Account created successfully. You can now log in.';
    exit;
}

if ($type === 'login') {
    $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        exit('Invalid credentials.');
    }

    $_SESSION['user_id'] = $user['id'];
    echo 'Login successful';
    exit;
}

if ($type === 'reset') {
    // Basic demo; you can replace this with an actual email system
    echo 'Password reset link would be sent to: ' . htmlspecialchars($email);
    exit;
}

exit('Unhandled request.');
