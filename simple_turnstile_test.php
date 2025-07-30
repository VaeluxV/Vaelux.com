<?php
require_once __DIR__ . '/config/turnstile_keys.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Turnstile Test</title>
</head>
<body>
    <h1>Simple Turnstile Test</h1>
    
    <p>Site Key: <?php echo htmlspecialchars($turnstile_sitekey); ?></p>
    <p>Secret Key: <?php echo htmlspecialchars($turnstile_secret); ?></p>
    
    <form id="test-form">
        <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars($turnstile_sitekey); ?>"></div>
        <button type="submit">Test Submit</button>
    </form>
    
    <div id="result"></div>
    
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        document.getElementById('test-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Wait a bit for Turnstile to be ready
            setTimeout(() => {
                const token = document.querySelector('[name="cf-turnstile-response"]');
                const resultDiv = document.getElementById('result');
                
                if (token && token.value) {
                    resultDiv.innerHTML = '<p style="color: green;">✅ Token found: ' + token.value.substring(0, 20) + '...</p>';
                    console.log('Full token:', token.value);
                } else {
                    resultDiv.innerHTML = '<p style="color: red;">❌ No token found</p>';
                    console.log('No token found');
                }
            }, 1000);
        });
        
        // Debug Turnstile loading
        window.addEventListener('load', function() {
            console.log('Page loaded');
            setTimeout(() => {
                console.log('Turnstile object:', window.turnstile);
                console.log('Turnstile widgets:', document.querySelectorAll('.cf-turnstile'));
            }, 2000);
        });
    </script>
</body>
</html> 