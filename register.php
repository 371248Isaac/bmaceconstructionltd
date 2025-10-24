<?php
// =======================
// BMACE Admin Registration
// =======================
include 'db.php'; // include InfinityFree connection file

$message = "";
$messageClass = "";
$redirect = false;

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match!";
        $messageClass = "error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email or username already exists
        $check_sql = "SELECT * FROM admins WHERE email=? OR username=?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "⚠️ Email or Username already exists!";
            $messageClass = "error";
        } else {
            // Insert new admin record
            $sql = "INSERT INTO admins (fullname, email, username, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $fullname, $email, $username, $hashed_password);

            if ($stmt->execute()) {
                $message = "✅ Admin account created successfully! Redirecting to login...";
                $messageClass = "success";
                $redirect = true;
            } else {
                $message = "❌ Error: Could not create account. Please try again.";
                $messageClass = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Register - BMACE Construction Ltd</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    *{box-sizing:border-box;}
    body{
      font-family:'Segoe UI',sans-serif;
      background:#f5f7fa;
      margin:0;
      display:flex;
      justify-content:center;
      align-items:center;
      min-height:100vh;
    }
    .container{
      background:#fff;
      padding:2rem;
      border-radius:12px;
      box-shadow:0 0 20px rgba(0,0,0,0.1);
      width:100%;
      max-width:400px;
    }
    h2{
      margin-bottom:1rem;
      color:#333;
      text-align:center;
    }
    input{
      width:100%;
      padding:12px;
      margin:8px 0;
      border:1px solid #ccc;
      border-radius:8px;
      outline:none;
    }
    input:focus{border-color:#007bff;}
    button{
      width:100%;
      padding:12px;
      background-color:#28a745;
      color:white;
      border:none;
      border-radius:8px;
      font-weight:bold;
      margin-top:12px;
      cursor:pointer;
    }
    button:hover{background-color:#1f7a35;}
    .link{text-align:center;margin-top:1rem;}
    .link a{color:#28a745;text-decoration:none;}
    .password-field{
      position:relative;
    }
    .password-field i{
      position:absolute;
      top:50%;
      right:12px;
      transform:translateY(-50%);
      cursor:pointer;
      color:#666;
    }
    .message{
      text-align:center;
      padding:10px;
      border-radius:6px;
      margin-bottom:10px;
    }
    .message.success{
      background:#d4edda;
      color:#155724;
    }
    .message.error{
      background:#f8d7da;
      color:#721c24;
    }
    .brand {
      text-align:center;
      margin-bottom:1rem;
    }
    .brand img {
      height:50px;
      display:block;
      margin:0 auto;
    }
    .brand span {
      font-size:1.1rem;
      font-weight:bold;
      color:#333;
      display:block;
      margin-top:0.5rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="brand">
      <img src="logo.png" alt="BMACE Logo" />
      <span>BMACE CONSTRUCTION LTD</span>
    </div>

    <h2><i class="fa fa-user-plus"></i> Admin Registration</h2>

    <?php if(!empty($message)): ?>
      <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="fullname" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="text" name="username" placeholder="Username" required />

      <div class="password-field">
        <input type="password" name="password" id="password" placeholder="Password" required />
        <i class="fa fa-eye" id="togglePassword"></i>
      </div>
      <div class="password-field">
        <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required />
        <i class="fa fa-eye" id="toggleConfirmPassword"></i>
      </div>

      <button type="submit">Create Account</button>
    </form>

    <div class="link">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>

  <script>
    // Toggle password visibility
    function setupToggle(toggleId, inputId) {
      const toggle = document.getElementById(toggleId);
      const input = document.getElementById(inputId);
      toggle.addEventListener('click', () => {
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        toggle.classList.toggle('fa-eye-slash', type === 'text');
        toggle.classList.toggle('fa-eye', type === 'password');
      });
    }

    setupToggle('togglePassword', 'password');
    setupToggle('toggleConfirmPassword', 'confirmPassword');

    <?php if ($redirect): ?>
      // Redirect after 3 seconds
      setTimeout(() => {
        window.location.href = "login.php";
      }, 3000);
    <?php endif; ?>
  </script>
</body>
</html>
