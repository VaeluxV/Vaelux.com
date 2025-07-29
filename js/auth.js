document.addEventListener("DOMContentLoaded", () => {
  const signupForm = document.getElementById("signup-form");
  const loginForm = document.getElementById("login-form");

  if (signupForm) {
    signupForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const payload = {
        username: signupForm.username.value.trim(),
        email: signupForm.email.value.trim(),
        password: signupForm.password.value,
        display_name: signupForm.display_name.value.trim()
      };

      try {
        const res = await fetch("/api/signup.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
          alert("Signup successful! You can now log in.");
          window.location.href = "/login.php";
        } else {
          alert(data.error || "Signup failed.");
        }
      } catch (err) {
        console.error("Signup error:", err);
        alert("An error occurred.");
      }
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const payload = {
        email: loginForm.email.value.trim(),
        password: loginForm.password.value
      };

      try {
        const res = await fetch("/api/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
          alert(`Welcome back, ${data.username}!`);
          window.location.href = "/dashboard.php";
        } else {
          alert(data.error || "Login failed.");
        }
      } catch (err) {
        console.error("Login error:", err);
        alert("An error occurred.");
      }
    });
  }
});
