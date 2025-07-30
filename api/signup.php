<?php
// Prevent any output before JSON response
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/email_service.php';
    require_once __DIR__ . '/../config/email_credentials.php';

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

    // Helper to generate verification token
    function generateVerificationToken() {
        return bin2hex(random_bytes(32));
    }

    // Validate and sanitize inputs
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }
    
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $displayName = trim($data['display_name'] ?? '');

    // Basic validation
    if (!$username || !$email || !$password || !$displayName) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    // Validate username and display name length
    if (strlen($username) < 3 || strlen($username) > 32) {
        http_response_code(400);
        echo json_encode(['error' => 'Username must be between 3 and 32 characters']);
        exit;
    }

    if (strlen($displayName) < 3 || strlen($displayName) > 32) {
        http_response_code(400);
        echo json_encode(['error' => 'Display name must be between 3 and 32 characters']);
        exit;
    }

    // Validate password strength
    if (strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 8 characters long']);
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

    // Generate UUID, hash password, and verification token
    $uuid = generateUniqueUUID($db);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken = generateVerificationToken();

    // Set default profile picture
    $defaultProfilePic = '/images/user_content/pre-made/profile_pictures/Eurostar-PBKA.jpg';

    // Insert user with verification token
    $stmt = $db->prepare("INSERT INTO users (uuid, username, email, password_hash, display_name, profile_picture, display_name_changes, display_name_last_changed, email_verified, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$uuid, $username, $email, $passwordHash, $displayName, $defaultProfilePic, 0, null, 0, $verificationToken]);

    // Get the user ID that was just created
    $userId = $db->lastInsertId();

    // Initialize email service and send verification email
    $emailService = new EmailService();
    $emailSent = $emailService->sendVerificationEmail($email, $username, $userId, $verificationToken);

    // Check if we're in development mode
    $isDevelopment = $email_config['development_mode'] ?? false;

    if ($emailSent) {
        if ($isDevelopment) {
            echo json_encode([
                'success' => true, 
                'message' => 'Account created successfully! Check the development log for verification link.',
                'email' => $email,
                'development_mode' => true
            ]);
        } else {
            echo json_encode([
                'success' => true, 
                'message' => 'Account created successfully! Please check your email to verify your account before logging in.',
                'email' => $email
            ]);
        }
    } else {
        echo json_encode([
            'success' => true, 
            'message' => 'Account created successfully! However, there was an issue sending the verification email. Please contact support.',
            'email' => $email
        ]);
    }

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Signup error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode(['error' => 'An internal error occurred. Please try again later.']);
}
