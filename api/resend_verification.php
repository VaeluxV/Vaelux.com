<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/email_service.php';
require_once __DIR__ . '/../config/turnstile_keys.php';

// Helper to generate verification token
function generateVerificationToken() {
    return bin2hex(random_bytes(32));
}

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
$turnstileToken = $data['cf-turnstile-response'] ?? '';

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
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

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Look up user by email
$stmt = $db->prepare("SELECT id, username, email_verified FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'No account found with this email address']);
    exit;
}

if ($user['email_verified']) {
    http_response_code(400);
    echo json_encode(['error' => 'This account is already verified']);
    exit;
}

// Generate new verification token
$newToken = generateVerificationToken();

// Update user with new token
$stmt = $db->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
$stmt->execute([$newToken, $user['id']]);

// Initialize email service and send verification email
$emailService = new EmailService();
$emailSent = $emailService->sendVerificationEmail($email, $user['username'], $user['id'], $newToken);

if ($emailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Verification email sent successfully! Please check your email and click the verification link.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send verification email. Please try again later.'
    ]);
} 