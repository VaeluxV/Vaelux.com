<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Signup</title>
  <link rel="stylesheet" href="/css/main_stylesheet.css">
  <style>
    form {
      margin-bottom: 2rem;
      max-width: 400px;
      margin-inline: auto;
    }
    input {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      margin: 8px 0;
    }
    button {
      padding: 10px;
      font-size: 1rem;
      width: 100%;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <main>
    <?php include __DIR__ . '/../headers/main_header.php'; ?>
    <h1 style="text-align: center;">Login</h1>
    <form id="login-form">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <h1 style="text-align: center;">Signup</h1>
    <form id="signup-form">
      <input type="text" name="username" placeholder="Username" required>
      <input type="text" name="display_name" placeholder="Display Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
    </form>
  </main>

  <script src="/js/auth.js"></script>
  <?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>
</html>
