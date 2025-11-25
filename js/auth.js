document.addEventListener("DOMContentLoaded", () => {
  const signupForm = document.getElementById("signup-form");
  const loginForm = document.getElementById("login-form");
  const resendVerificationForm = document.getElementById("resend-verification-form");

  // Wait for Turnstile to be ready
  function waitForTurnstile() {
    return new Promise((resolve) => {
      if (window.turnstile) {
        resolve();
      } else {
        const checkTurnstile = setInterval(() => {
          if (window.turnstile) {
            clearInterval(checkTurnstile);
            resolve();
          }
        }, 100);
      }
    });
  }

  // Helper function to show messages
  function showMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.auth-message');
    existingMessages.forEach(msg => msg.remove());

    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `auth-message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
      padding: 1rem;
      margin: 1rem 0;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
      ${type === 'success' ? 
        'background: #1a4d1a; color: #90ee90; border: 1px solid #4caf50;' : 
        type === 'error' ?
        'background: #4d1a1a; color: #ffb3b3; border: 1px solid #f44336;' :
        'background: #1a1a4d; color: #b3b3ff; border: 1px solid #2196f3;'
      }
    `;

    // Insert at the top of the auth container
    const authContainer = document.querySelector('.auth-container');
    if (authContainer) {
      authContainer.insertBefore(messageDiv, authContainer.firstChild);
    }

    // Auto-remove after 10 seconds for success/info messages
    if (type !== 'error') {
      setTimeout(() => {
        if (messageDiv.parentNode) {
          messageDiv.remove();
        }
      }, 10000);
    }
  }

  if (signupForm) {
    signupForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Wait for Turnstile to be ready
      await waitForTurnstile();

      // Temporarily disable Turnstile requirement for debugging
      /*
      // Get Turnstile token
      const turnstileResponse = signupForm.querySelector('[name="cf-turnstile-response"]');
      if (!turnstileResponse || !turnstileResponse.value) {
        showMessage("Please complete the security check.", 'error');
        return;
      }

      console.log("Turnstile token:", turnstileResponse.value); // Debug log
      */

      const payload = {
        username: signupForm.username.value.trim(),
        email: signupForm.email.value.trim(),
        password: signupForm.password.value,
        display_name: signupForm.display_name.value.trim()
        // 'cf-turnstile-response': turnstileResponse.value
      };

      try {
        const res = await fetch("/api/signup.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
          showMessage(data.message, 'success');
          signupForm.reset();
          // Reset Turnstile widget
          if (window.turnstile) {
            window.turnstile.reset();
          }
          
          // Don't redirect immediately - let user see the message
          // They need to verify their email first
        } else {
          showMessage(data.error || "Signup failed.", 'error');
          // Reset Turnstile widget on error
          if (window.turnstile) {
            window.turnstile.reset();
          }
        }
      } catch (err) {
        console.error("Signup error:", err);
        showMessage("An error occurred during signup. Please try again.", 'error');
        // Reset Turnstile widget on error
        if (window.turnstile) {
          window.turnstile.reset();
        }
      }
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Wait for Turnstile to be ready
      await waitForTurnstile();

      // Temporarily disable Turnstile requirement for debugging
      /*
      // Get Turnstile token
      const turnstileResponse = loginForm.querySelector('[name="cf-turnstile-response"]');
      if (!turnstileResponse || !turnstileResponse.value) {
        showMessage("Please complete the security check.", 'error');
        return;
      }

      console.log("Turnstile token:", turnstileResponse.value); // Debug log
      */

      const payload = {
        email: loginForm.email.value.trim(),
        password: loginForm.password.value
        // 'cf-turnstile-response': turnstileResponse.value
      };

      try {
        const res = await fetch("/api/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
          window.location.href = '/dashboard';
          return;
        } else {
          showMessage(data.message || data.error || "Login failed.", 'error');
          // Reset Turnstile widget on error
          if (window.turnstile) {
            window.turnstile.reset();
          }
        }
      } catch (err) {
        console.error("Login error:", err);
        showMessage("An error occurred during login. Please try again.", 'error');
        // Reset Turnstile widget on error
        if (window.turnstile) {
          window.turnstile.reset();
        }
      }
    });
  }

  if (resendVerificationForm) {
    resendVerificationForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Wait for Turnstile to be ready
      await waitForTurnstile();

      // Temporarily disable Turnstile requirement for debugging
      /*
      // Get Turnstile token
      const turnstileResponse = resendVerificationForm.querySelector('[name="cf-turnstile-response"]');
      if (!turnstileResponse || !turnstileResponse.value) {
        showMessage("Please complete the security check.", 'error');
        return;
      }

      console.log("Turnstile token:", turnstileResponse.value); // Debug log
      */

      const payload = {
        email: resendVerificationForm.email.value.trim()
        // 'cf-turnstile-response': turnstileResponse.value
      };

      try {
        const res = await fetch("/api/resend_verification.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
          showMessage(data.message, 'success');
          resendVerificationForm.reset();
          // Reset Turnstile widget
          if (window.turnstile) {
            window.turnstile.reset();
          }
        } else {
          showMessage(data.error || "Failed to resend verification email.", 'error');
          // Reset Turnstile widget on error
          if (window.turnstile) {
            window.turnstile.reset();
          }
        }
      } catch (err) {
        console.error("Resend verification error:", err);
        showMessage("An error occurred while resending verification email. Please try again.", 'error');
        // Reset Turnstile widget on error
        if (window.turnstile) {
          window.turnstile.reset();
        }
      }
    });
  }
});
