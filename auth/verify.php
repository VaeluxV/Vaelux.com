<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$userId = $_GET['id'] ?? '';
$token = $_GET['token'] ?? '';
$message = '';
$success = false;

if ($userId && $token) {
    // Look up user by ID and verification token
    $stmt = $db->prepare("SELECT id, username, email FROM users WHERE id = ? AND verification_token = ? AND email_verified = 0");
    $stmt->execute([$userId, $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Mark email as verified and clear token
        $stmt = $db->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        $success = true;
        $message = "Email verified successfully! You can now log in to your account.";
    } else {
        $message = "Invalid or expired verification link. Please try signing up again or contact support.";
    }
} else {
    $message = "Invalid verification link. Missing user ID or token.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/auth_stylesheet.css">
    <style>
        .verification-container {
            max-width: 600px;
            margin: 4rem auto;
            padding: 2rem;
            background: #23272a;
            border-radius: 12px;
            box-shadow: 0 2px 16px #0008;
            text-align: center;
        }
        .verification-message {
            margin: 2rem 0;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1.1rem;
        }
        .verification-success {
            background: #1a4d1a;
            color: #90ee90;
            border: 1px solid #4caf50;
        }
        .verification-error {
            background: #4d1a1a;
            color: #ffb3b3;
            border: 1px solid #f44336;
        }
        .verification-actions {
            margin-top: 2rem;
        }
        .verification-actions a {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 0.5rem;
            background: #c9085c;
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            transition: background 0.2s;
        }
        .verification-actions a:hover {
            background: #b50c33;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../headers/main_header.php'; ?>
    
    <main>
        <div class="verification-container">
            <h1>Email Verification</h1>
            
            <div class="verification-message <?php echo $success ? 'verification-success' : 'verification-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            
            <div class="verification-actions">
                <?php if ($success): ?>
                    <a href="/auth">Go to Login</a>
                <?php else: ?>
                    <a href="/auth">Back to Login</a>
                    <a href="/auth">Sign Up Again</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>
</html> 