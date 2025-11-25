<?php
// Email Service Class
// Handles sending emails using Gmail SMTP directly

require_once __DIR__ . '/../config/email_credentials.php';

class EmailService {
    private $config;
    private $templates;
    
    public function __construct() {
        global $email_config, $email_templates;
        $this->config = $email_config;
        $this->templates = $email_templates;
    }
    
    /**
     * Send verification email
     */
    public function sendVerificationEmail($email, $username, $userId, $token) {
        try {
            // Get the host - use localhost as fallback for CLI
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $verificationLink = "http://" . $host . "/auth/verify.php?id=" . $userId . "&token=" . $token;
            
            $subject = $this->templates['verification']['subject'];
            $body = $this->templates['verification']['body_template'];
            
            // Replace placeholders
            $body = str_replace('{username}', htmlspecialchars($username), $body);
            $body = str_replace('{verification_link}', $verificationLink, $body);
            
            // Check if we're in development mode
            if ($this->config['development_mode']) {
                return $this->sendEmailDevelopment($email, $subject, $body, $verificationLink);
            } else {
                return $this->sendEmail($email, $subject, $body);
            }
        } catch (Exception $e) {
            error_log("Email service error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Development mode email handling
     */
    private function sendEmailDevelopment($email, $subject, $body, $verificationLink) {
        // Log the verification link for development
        $logMessage = "\n" . str_repeat("=", 80) . "\n";
        $logMessage .= "DEVELOPMENT MODE - EMAIL VERIFICATION\n";
        $logMessage .= str_repeat("=", 80) . "\n";
        $logMessage .= "To: " . $email . "\n";
        $logMessage .= "Subject: " . $subject . "\n";
        $logMessage .= "Verification Link: " . $verificationLink . "\n";
        $logMessage .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $logMessage .= str_repeat("=", 80) . "\n";
        
        // Log to error log
        error_log($logMessage);
        
        // Also create a development log file
        $devLogFile = __DIR__ . '/../logs/email_verification.log';
        $logDir = dirname($devLogFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($devLogFile, $logMessage, FILE_APPEND | LOCK_EX);
        
        return true; // Always return true in development mode
    }
    
    /**
     * Send email using Gmail SMTP
     */
    private function sendEmail($to, $subject, $body) {
        try {
            // Set up email headers
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>',
                'Reply-To: ' . $this->config['reply_to'],
                'X-Mailer: Vaelux.com Email Service',
                'X-Priority: 1',
                'X-MSMail-Priority: High',
                'Importance: High'
            ];
            
            // Configure PHP mail settings for Gmail SMTP
            ini_set('SMTP', $this->config['smtp_host']);
            ini_set('smtp_port', $this->config['smtp_port']);
            ini_set('sendmail_from', $this->config['from_email']);
            
            // Try to send email
            $result = mail($to, $subject, $body, implode("\r\n", $headers));
            
            if (!$result) {
                error_log("Email sending failed for: $to");
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Alternative method using fsockopen for direct SMTP connection
     */
    public function sendEmailDirectSMTP($to, $subject, $body) {
        try {
            $smtp_host = $this->config['smtp_host'];
            $smtp_port = $this->config['smtp_port'];
            $username = $this->config['smtp_username'];
            $password = $this->config['smtp_password'];
            
            // Create SMTP connection
            $socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
            
            if (!$socket) {
                error_log("SMTP connection failed: $errstr ($errno)");
                return false;
            }
            
            // Read server response
            $response = fgets($socket, 515);
            
            // EHLO command
            fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $response = fgets($socket, 515);
            
            // Start TLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            
            // Upgrade to TLS
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("TLS upgrade failed");
                fclose($socket);
                return false;
            }
            
            // EHLO again after TLS
            fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $response = fgets($socket, 515);
            
            // Authenticate
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            
            fputs($socket, base64_encode($username) . "\r\n");
            $response = fgets($socket, 515);
            
            fputs($socket, base64_encode($password) . "\r\n");
            $response = fgets($socket, 515);
            
            // Set sender
            fputs($socket, "MAIL FROM: <" . $this->config['from_email'] . ">\r\n");
            $response = fgets($socket, 515);
            
            // Set recipient
            fputs($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 515);
            
            // Send data
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            
            // Email headers and body
            $email_data = "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">\r\n";
            $email_data .= "To: $to\r\n";
            $email_data .= "Subject: $subject\r\n";
            $email_data .= "MIME-Version: 1.0\r\n";
            $email_data .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_data .= "X-Mailer: Vaelux.com Email Service\r\n";
            $email_data .= "\r\n";
            $email_data .= $body . "\r\n";
            $email_data .= ".\r\n";
            
            fputs($socket, $email_data);
            $response = fgets($socket, 515);
            
            // Quit
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return strpos($response, '250') === 0; // Check if email was accepted
        } catch (Exception $e) {
            error_log("Direct SMTP error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test email configuration
     */
    public function testConnection() {
        try {
            if ($this->config['development_mode']) {
                return ['success' => true, 'message' => 'Development mode active - emails will be logged instead of sent'];
            }
            
            $smtp_host = $this->config['smtp_host'];
            $smtp_port = $this->config['smtp_port'];
            
            // Test basic connection
            $socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 10);
            
            if (!$socket) {
                return ['success' => false, 'message' => "SMTP connection failed: $errstr ($errno)"];
            }
            
            fclose($socket);
            return ['success' => true, 'message' => 'SMTP connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'SMTP test error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get verification links from development log
     */
    public function getDevelopmentLog() {
        $devLogFile = __DIR__ . '/../logs/email_verification.log';
        
        if (file_exists($devLogFile)) {
            return file_get_contents($devLogFile);
        }
        
        return "No verification emails logged yet.";
    }
}
?> 