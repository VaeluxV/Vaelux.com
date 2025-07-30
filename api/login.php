<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config/turnstile_keys.php';

// Helper to verify Turnstile token
function verifyTurnstileToken($token) {
    global $turnstile_secretkey;
    
    // Debug: Log the token and secret key
    error_log("Turnstile verification - Token: " . substr($token, 0, 20) . "...");
    error_log("Turnstile verification - Secret key: " . substr($turnstile_secretkey, 0, 10) . "...");
    
    $data = [
        'secret' => $turnstile_secretkey,
        'response' => $token
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Debug: Log the response
    error_log("Turnstile verification - HTTP Code: " . $httpCode);
    error_log("Turnstile verification - Response: " . $response);
    
    $result = json_decode($response, true);
    $success = $result && isset($result['success']) && $result['success'];
    
    // Debug: Log the result
    error_log("Turnstile verification - Success: " . ($success ? 'true' : 'false'));
    
    return $success;
}

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$turnstileToken = $data['cf-turnstile-response'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing email or password']);
    exit;
}

    // Temporarily disable Turnstile verification for debugging
    /*
    if (!$turnstileToken) {
        http_response_code(400);
        echo json_encode(['error' => 'Please complete the security check']);
        exit;
    }

    // Verify Turnstile token
    if (!verifyTurnstileToken($turnstileToken)) {
        http_response_code(400);
        echo json_encode(['error' => 'Security check failed. Please try again.']);
        exit;
    }
    */

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
