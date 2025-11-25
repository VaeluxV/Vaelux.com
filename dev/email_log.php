<?php
// Development Email Log Viewer
// Only accessible in development mode

require_once __DIR__ . '/../config/email_credentials.php';
require_once __DIR__ . '/../includes/email_service.php';

// Only allow access in development mode
if (!$development_mode) {
    http_response_code(403);
    die('Access denied - development mode only');
}

$emailService = new EmailService();
$logContent = $emailService->getDevelopmentLog();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development - Email Verification Log</title>
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <style>
        .dev-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: #23272a;
            border-radius: 12px;
            box-shadow: 0 2px 16px #0008;
            color: #f3e0eb;
        }
        .dev-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #c9085c;
        }
        .dev-header h1 {
            color: #c9085c;
            margin-bottom: 0.5rem;
        }
        .dev-header p {
            color: #aaa;
            font-size: 1.1rem;
        }
        .log-content {
            background: #181a1b;
            padding: 1.5rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.4;
            white-space: pre-wrap;
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }
        .refresh-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #c9085c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 1rem 0;
            transition: background 0.2s;
        }
        .refresh-btn:hover {
            background: #b50c33;
        }
        .verification-link {
            color: #4CAF50;
            word-break: break-all;
        }
        .no-logs {
            text-align: center;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../headers/main_header.php'; ?>
    
    <main class="dev-container">
        <div class="dev-header">
            <h1>🔧 Development Mode</h1>
            <p>Email Verification Log Viewer</p>
            <p>This page shows verification links for local testing</p>
            <a href="?refresh=1" class="refresh-btn">🔄 Refresh Log</a>
        </div>
        
        <div class="log-content">
            <?php if (trim($logContent) === "No verification emails logged yet."): ?>
                <div class="no-logs">
                    No verification emails have been sent yet.<br>
                    Try creating a new account to see verification links here.
                </div>
            <?php else: ?>
                <?php echo htmlspecialchars($logContent); ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
            <p style="color: #666; font-size: 0.9rem;">
                💡 <strong>How to test:</strong><br>
                1. Create a new account on the signup page<br>
                2. Come back here to see the verification link<br>
                3. Click the verification link to verify the account<br>
                4. Then try logging in with the new account
            </p>
        </div>
    </main>
    
    <?php include __DIR__ . '/../footers/main_footer.php'; ?>
    
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html> 