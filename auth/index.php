<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login / Signup</title>
  <link rel="stylesheet" href="/css/main_stylesheet.css">
  <link rel="stylesheet" href="/css/auth_stylesheet.css">
</head>
<body>
  <main>
    <?php include __DIR__ . '/../headers/main_header.php'; ?>
    <?php require_once __DIR__ . '/../config/turnstile_keys.php'; ?>
    
    <div class="auth-container">
      <div class="auth-forms-wrapper">
        <div class="auth-form-container">
          <h1 class="auth-title">Login</h1>
          <form id="login-form" class="auth-form" method="POST" autocomplete="on">
            <input type="email" name="email" placeholder="Email" required autocomplete="email">
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            <div class="cf-turnstile" id="login-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstile_sitekey); ?>"></div>
            <button type="submit">Login</button>
          </form>
        </div>
        
        <div class="auth-form-container">
          <h1 class="auth-title">Signup</h1>
          <form id="signup-form" class="auth-form" method="POST" autocomplete="on">
            <input type="text" name="username" placeholder="Username" required autocomplete="username">
            <input type="text" name="display_name" placeholder="Display Name" required autocomplete="off">
            <input type="email" name="email" placeholder="Email" required autocomplete="email">
            <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
            <div class="cf-turnstile" id="signup-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstile_sitekey); ?>"></div>
            <button type="submit">Sign Up</button>
          </form>
        </div>
        
        <div class="auth-form-container auth-help-container">
          <h2 class="auth-title">Need Help?</h2>
          <form id="resend-verification-form" class="auth-form" method="POST">
            <p style="text-align: center; margin-bottom: 1rem; color: #aaa;">
              Didn't receive your verification email? Enter your email below to resend it.
            </p>
            <input type="email" name="email" placeholder="Enter your email" required>
            <div class="cf-turnstile" id="resend-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstile_sitekey); ?>"></div>
            <button type="submit">Resend Verification Email</button>
          </form>
        </div>
      </div>
    </div>
    
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  </main>
  <script src="/js/auth.js"></script>
  <?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>
</html>
