<?php
// Email System Credentials
// This file contains sensitive email configuration data

// Development mode - set to true for local development
$development_mode = true; // Set to false for production

// Gmail SMTP Configuration
$email_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'vaelux.com@gmail.com',
    'smtp_password' => 'lyjp xtwa fgjh jcis', // App password
    'smtp_encryption' => 'tls',
    'from_email' => 'vaelux.com@gmail.com',
    'from_name' => 'Vaelux.com account',
    'reply_to' => 'vaelux.com@gmail.com',
    'development_mode' => $development_mode
];

// Email templates
$email_templates = [
    'verification' => [
        'subject' => 'Verify your Vaelux.com account',
        'body_template' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #c9085c; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .button { display: inline-block; padding: 12px 24px; background: #c9085c; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Vaelux.com!</h1>
        </div>
        <div class="content">
            <h2>Hello {username},</h2>
            <p>Thank you for creating your account on Vaelux.com. To complete your registration, please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{verification_link}" class="button">Verify Email Address</a>
            </div>
            
            <p>If the button doesn\'t work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #666;">{verification_link}</p>
            
            <p>This verification link will expire in 24 hours for security reasons.</p>
            
            <p>If you didn\'t create this account, you can safely ignore this email.</p>
        </div>
        <div class="footer">
            <p>This email was sent from Vaelux.com account verification system.</p>
            <p>If you have any questions, please contact support.</p>
        </div>
    </div>
</body>
</html>'
    ]
];
?> 