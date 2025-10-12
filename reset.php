<?php
session_start();
include 'db.php'; // Connect to your BMACE database

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $errors[] = "No admin found with this email.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
        $updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $hashedPassword, $email);

        if ($updateStmt->execute()) {
            $success = "✅ Password reset successful! Redirecting to login page...";
        } else {
            $errors[] = "❌ Error updating password: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - BMACE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body { 
      font-family: 'Segoe UI', sans-serif; 
      background: #f5f7fa; 
      margin: 0; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      min-height: 100vh; 
    }
    .container { 
      background: #fff; 
      padding: 2rem; 
      border-radius: 12px; 
      box-shadow: 0 0 20px rgba(0,0,0,0.1); 
      width: 100%; 
      max-width: 400px; 
      text-align: center;
    }
    .logo {
      width: 80px;
      height: auto;
      margin-bottom: 10px;
    }
    h1 {
      font-size: 1.3rem;
      color:rgb(20, 21, 21);
      margin-bottom: 1.5rem;
    }
    h2 { 
      margin-bottom: 1rem; 
      color: #333; 
    }
    .input-group { position: relative; }
    input { 
      width: 100%; 
      padding: 12px; 
      margin: 8px 0; 
      border: 1px solid #ccc; 
      border-radius: 8px; 
      outline: none; 
    }
    input:focus { border-color: #198754; }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
    }
    button { 
      width: 100%; 
      padding: 12px; 
      background-color: #198754; 
      color: white; 
      border: none; 
      border-radius: 8px; 
      font-weight: bold; 
      margin-top: 12px; 
      cursor: pointer; 
      transition: 0.3s;
    }
    button:hover { background-color: #145c3f; }
    .link { text-align: center; margin-top: 1rem; }
    .link a { color: #198754; text-decoration: none; font-weight: 500; }
    .link a:hover { text-decoration: underline; }
    .error, .success {
      font-size: 0.95rem; 
      margin-bottom: 10px; 
      padding: 10px; 
      border-radius: 8px; 
    }
    .error { background: #ffe6e6; color: #d63031; }
    .success { background: #e8f5e9; color: #2e7d32; }
  </style>
</head>
<body>
  <div class="container">
    <img src="assets/logo.png" alt="BMACE Logo" class="logo">
    <h1>BMACE CONSTRUCTION LTD</h1>

    <h2><i class="fa fa-key"></i> Reset Password</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach($errors as $error) echo $error . "<br>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
        <script>
            setTimeout(() => {
                window.location.href = "login.php";
            }, 3000);
        </script>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Admin Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

      <div class="input-group">
        <input type="password" id="new_password" name="new_password" placeholder="New Password" required />
        <i class="fa fa-eye toggle-password" toggle="#new_password"></i>
      </div>

      <div class="input-group">
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
        <i class="fa fa-eye toggle-password" toggle="#confirm_password"></i>
      </div>

      <button type="submit">Reset Password</button>
    </form>

    <div class="link">
      Remembered your password? <a href="login.php">Login</a>
    </div>
  </div>

  <script>
    // Password toggle
    const toggleIcons = document.querySelectorAll('.toggle-password');
    toggleIcons.forEach(icon => {
      icon.addEventListener('click', function() {
        const input = document.querySelector(this.getAttribute('toggle'));
        if (input.type === "password") {
          input.type = "text";
          this.classList.remove('fa-eye');
          this.classList.add('fa-eye-slash');
        } else {
          input.type = "password";
          this.classList.remove('fa-eye-slash');
          this.classList.add('fa-eye');
        }
      });
    });
  </script>
</body>
</html>