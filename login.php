<?php
// Start session
session_start();

// Include DB connection
include 'db.php'; // Make sure this file connects to bmace_admin DB

$error = "";
$email = ""; // Initialize for persistence
$password_value = ""; // To retain password temporarily if desired (optional)

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin by email
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['fullname'];
            $_SESSION['admin_email'] = $admin['email'];

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login - BMACE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 420px;
      text-align: center;
    }
    .brand {
      margin-bottom: 1rem;
    }
    .brand img {
      height: 50px;
    }
    .brand span {
      font-size: 1.1rem;
      font-weight: bold;
      color: #333;
      display: block;
      margin-top: 0.5rem;
    }
    h2 {
      margin-bottom: 1rem;
      color: #333;
    }
    .input-group {
      position: relative;
      width: 100%;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
      font-size: 1rem;
    }
    input:focus {
      border-color: #28a745;
    }
    .toggle-password {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
      font-size: 1rem;
    }
    .toggle-password:hover {
      color: #28a745;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      margin-top: 12px;
      cursor: pointer;
    }
    button:hover {
      background-color: #1e7e34;
    }
    .link {
      text-align: center;
      margin-top: 1rem;
    }
    .link a {
      color: #28a745;
      text-decoration: none;
    }
    .error {
      color: red;
      font-size: 0.95rem;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="brand">
      <img src="logo.png" alt="BMACE Logo" />
      <span>BMACE CONSTRUCTION LTD</span>
    </div>
    <h2><i class="fa fa-user-lock"></i> Admin Login</h2>
    <form method="POST" id="loginForm">
      <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <!-- Preserve input values -->
      <input type="email" name="email" id="email" placeholder="Email" required 
             value="<?= htmlspecialchars($email) ?>" />

      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Password" required 
               value="<?= htmlspecialchars($password_value) ?>" />
        <i class="fa-solid fa-eye-slash toggle-password" id="togglePassword"></i>
      </div>

      <div class="link">
        <a href="reset.php">Forgot password</a>
      </div>
      <button type="submit">Login</button>
    </form>
    <div class="link">
      Don't have an account? <a href="register.php">Register</a>
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      togglePassword.classList.toggle('fa-eye');
      togglePassword.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>