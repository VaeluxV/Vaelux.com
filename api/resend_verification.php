<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/email_service.php';

// Helper to generate verification token
function generateVerificationToken() {
    return bin2hex(random_bytes(32));
}

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit;
}

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