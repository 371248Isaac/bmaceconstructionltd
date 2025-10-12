<?php
session_start(); // Start session at the very top

// Check if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    // Not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
// After verifying login credentials
//$_SESSION['admin_email'] = $email; // store admin email in session
//header("Location: login.php"); // redirect to dashboard
//exit();

// Include your database connection
include 'db.php';
// ================= Handle Project Upload =================
$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) 
{
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = "Completed";

    $imagePath = "";
    if (!empty($_FILES['image']['name'])) 
    {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $imagePath = $targetDir . time() . "_" . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $stmt = $conn->prepare("INSERT INTO projects (title, description, image, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $description, $imagePath, $status);
            if ($stmt->execute()) {
                $message = "✅ Project uploaded successfully!";
                $messageClass = "success";
            } else {
                $message = "❌ Database error while saving project.";
                $messageClass = "error";
            }
            $stmt->close();
        } else {
            $message = "❌ Failed to upload image.";
            $messageClass = "error";
        }
    } else 
    {
        $message = "❌ No image selected.";
        $messageClass = "error";
    }
}

// ================= Handle Ongoing Project Upload =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ongoing'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $expected_end_date = $_POST['expected_end_date'];

    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $imagePath = $targetDir . time() . "_" . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $stmt = $conn->prepare("INSERT INTO ongoing_projects (title, description, image, start_date, expected_end_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $title, $description, $imagePath, $start_date, $expected_end_date);
            if ($stmt->execute()) {
                $message = "✅ Ongoing project uploaded successfully!";
                $messageClass = "success";
            } else {
                $message = "❌ Database error while saving ongoing project.";
                $messageClass = "error";
            }
            $stmt->close();
        } else {
            $message = "❌ Failed to upload image.";
            $messageClass = "error";
        }
    } else {
        $message = "❌ No image selected.";
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BMACE Admin Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    /* ==== SIDEBAR ==== */
    * { box-sizing: border-box; margin:0; padding:0; }
    body { font-family: 'Segoe UI', sans-serif; background:#f0f2f5; display:flex; }
    .sidebar { width:260px; background:#1e1e2f; color:#fff; height:100vh; position:fixed; left:0; top:0; overflow-y:auto; }
    .sidebar .logo { display:flex; align-items:center; justify-content:center; padding:1rem; background:#151521; border-bottom:1px solid #333; font-weight:bold; font-size:1rem; }
    .sidebar nav a, #logoutBtn { display:block; color:#ccc; padding:1rem 1.5rem; text-decoration:none; border-left:4px solid transparent; background:none; border:none; text-align:left; width:100%; font-size:1rem; }
    .sidebar nav a:hover, .sidebar nav a.active, #logoutBtn:hover { background:#292944; color:#fff; border-left:4px solid #00bcd4; cursor:pointer; }
    /* ==== HEADER ==== */
    header { width:100%; background:#fff; padding:1rem 2rem; box-shadow:0 2px 4px rgba(0,0,0,0.1); position:fixed; left:260px; top:0; display:flex; justify-content:space-between; align-items:center; }
    .brand { display:flex; align-items:center; gap:12px; }
    .brand img { height:40px; }
    /* ==== MAIN CONTENT ==== */
    main { margin-left:260px; padding:6rem 2rem 2rem; width:100%; }
    .card { background:#fff; padding:1.5rem; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.05); margin-bottom:2rem; }
    h2 { margin-bottom:1rem; font-size:1.25rem; color:#444; }
    table { width:100%; border-collapse:collapse; }
    table th, table td { padding:0.75rem; border:1px solid #eee; text-align:left; }
    .btn { padding:8px 16px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
    .btn-primary { background:#007bff; color:#fff; margin-bottom:10px; display:inline-block; text-decoration:none; }
    .btn-danger { background:#dc3545; color:#fff; }
    form input, form textarea { width:100%; padding:0.75rem; margin:0.5rem 0; border:1px solid #ccc; border-radius:5px; }
    .career-bg { background:#f9f9f9; padding:1rem; border-radius:8px; }
    .password-field { position:relative; }
    .password-field i { position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#666; }
  /* ✅ ensure images are 100px wide */
    img.thumb { width:100px; height:auto; border-radius:4px; }

    /* ✅ responsiveness fix */
    @media (max-width: 768px) {
      body { flex-direction:column; }
      .sidebar { width:100%; height:auto; position:relative; }
      header { left:0; width:100%; }
      main { margin-left:0; padding:5rem 1rem 1rem; }
      table { font-size:0.85rem; }
      table th, table td { padding:0.5rem; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo">BMACE CONSTRUCTION LTD</div>
    <nav>
      <a href="#contact" class="active"><i class="fa fa-envelope"></i> Contact Messages</a>
      <a href="#career"><i class="fa fa-briefcase"></i> Career Submissions</a>
      <a href="#projects"><i class="fa fa-building"></i> Our Projects</a>
      <a href="#ongoing"><i class="fa fa-spinner"></i> Ongoing Projects</a>
      <a href="#reset"><i class="fa fa-key"></i> Reset Password</a>
      <button id="logoutBtn"><i class="fa fa-sign-out-alt"></i> Logout</button>
    </nav>
  </div>

  <header>
    <div class="brand">
      <img src="BMACE_OFFICIAL_IMAGES/BMACELOGO.jpg" alt="Logo">
      <strong><font color="blue">BMACE</font> <font color="red">CONSTRUCTION LTD</font></strong>
    </div>
  </header>

  <main>
    <!-- Display global message if exists -->
    <?php if (!empty($message)): ?>
      <div class="alert <?php echo ($messageClass === 'success') ? 'success' : 'error'; ?>">
        <?php echo htmlentities($message, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <!-- ================= Contact Messages ================= -->
    <section id="contact" class="card">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Contact Messages</h2>
        <div>
          <a class="btn btn-primary" href="download_pdf.php?table=contact_messages" target="_blank">Download PDF</a>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Received</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // show full contact fields from contact_messages table
          $res = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
          if($res){
            while($row = $res->fetch_assoc()){
                $id = (int)$row['id'];
                echo "<tr>
                <td>".htmlentities($row['name'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlentities($row['email'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlentities($row['phone'], ENT_QUOTES, 'UTF-8')."</td>
                
                <td>".nl2br(htmlentities($row['message'], ENT_QUOTES, 'UTF-8'))."</td>
                <td class='small'>".htmlentities($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8')."</td>
                <td><a class='btn btn-danger' href='delete.php?table=contact_messages&id={$id}' onclick=\"return confirm('Delete this contact message?');\">Delete</a></td>
                </tr>";
            }
          } else {
            echo "<tr><td colspan='7'>No contact messages found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <!-- ================= Career Submissions ================= -->
    <section id="career" class="card">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Career Submissions</h2>
        <div>
          <a class="btn btn-primary" href="download_pdf.php?table=career_applications" target="_blank">Download PDF</a>
        </div>
      </div>

      <div class="career-bg">
        <table>
          <thead>
            <tr>
              <th>Full Name</th><th>Email</th><th>Phone</th><th>Position</th><th>Home Address</th>
              <th>Gender</th><th>Marital Status</th><th>Country</th><th>Qualifications</th><th>Resume</th><th>Received</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $res = $conn->query("SELECT * FROM career_applications ORDER BY created_at DESC");
            if($res){
              while($row = $res->fetch_assoc()){
                  $id = (int)$row['id'];
                  $resume = !empty($row['resume']) ? $row['resume'] : '#';
                  echo "<tr>
                  <td>".htmlentities($row['fullname'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['email'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['phone'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['position'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['home_address'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['gender'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['marital_status'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".htmlentities($row['country'], ENT_QUOTES, 'UTF-8')."</td>
                  <td>".nl2br(htmlentities($row['qualifications'], ENT_QUOTES, 'UTF-8'))."</td>
                  <td><a href='".htmlspecialchars($resume, ENT_QUOTES, 'UTF-8')."' target='_blank'>View</a></td>
                  <td class='small'>".htmlentities($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8')."</td>
                  <td><a class='btn btn-danger' href='delete.php?table=career_applications&id={$id}' onclick=\"return confirm('Delete this application?');\">Delete</a></td>
                  </tr>";
              }
            } else {
              echo "<tr><td colspan='12'>No career submissions found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ================= Completed Projects ================= -->
    <section id="projects" class="card">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Completed Projects</h2>
        <div>
          <a class="btn btn-primary" href="download_pdf.php?table=projects" target="_blank">Download PDF</a>
        </div>
      </div>

      <!-- Upload Form -->
      <form action="" method="POST" enctype="multipart/form-data" style="margin-bottom:1rem;">
        <input type="text" name="title" placeholder="Project Title" required>
        <textarea name="description" placeholder="Project Description" required></textarea>
        <input type="file" name="image" accept="image/*" width='100' required>
        <button type="submit" name="add_project" class="btn btn-primary">Add Project</button>
      </form>

      <table>
        <thead><tr><th>Title</th><th>Description</th><th>Image</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          <?php
          $res = $conn->query("SELECT * FROM projects WHERE status='Completed' ORDER BY created_at DESC");
          if($res){
            while($row = $res->fetch_assoc()){
                $id = (int)$row['id'];
                $img = !empty($row['image']) ? $row['image'] : '';
                $imgTag = $img ? "<img src='".htmlspecialchars($img, ENT_QUOTES, 'UTF-8')."' class='thumb' 
                alt='project image'>" : '';
                echo "<tr>
                <td>".htmlentities($row['title'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".nl2br(htmlentities($row['description'], ENT_QUOTES, 'UTF-8'))."</td>
                <td>{$imgTag}</td>
                <td>".htmlentities($row['status'], ENT_QUOTES, 'UTF-8')."</td>
                <td><a class='btn btn-danger' href='delete.php?table=projects&id={$id}' onclick=\"return confirm('Delete this project?');\">Delete</a></td>
                </tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No completed projects found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <!-- ================= Ongoing Projects ================= -->
    <section id="ongoing" class="card">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Ongoing Projects</h2>
        <div>
          <a class="btn btn-primary" href="download_pdf.php?table=ongoing_projects" target="_blank">Download PDF</a>
        </div>
      </div>

      <!-- Upload Form -->
      <form action="" method="POST" enctype="multipart/form-data" style="margin-bottom:1rem;">
        <input type="text" name="title" placeholder="Project Title" required>
        <textarea name="description" placeholder="Project Description" required></textarea>
        <input type="date" name="start_date" required>
        <input type="date" name="expected_end_date" required>
        <input type="file" name="image" accept="image/*" required size="10px">
        <button type="submit" name="add_ongoing" class="btn btn-primary">Add Ongoing Project</button>
      </form>

      <table>
        <thead><tr><th>Title</th><th>Description</th><th>Start Date</th><th>Expected End Date</th><th>Image</th><th>Action</th></tr></thead>
        <tbody>
          <?php
          $res = $conn->query("SELECT * FROM ongoing_projects ORDER BY created_at DESC");
          if($res){
            while($row = $res->fetch_assoc()){
                $id = (int)$row['id'];
                $img = !empty($row['image']) ? $row['image'] : '';
                $imgTag = $img ? "<img src='".htmlspecialchars($img, ENT_QUOTES, 'UTF-8')."' class='thumb' alt='ongoing image'>" : '';
                echo "<tr>
                <td>".htmlentities($row['title'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".nl2br(htmlentities($row['description'], ENT_QUOTES, 'UTF-8'))."</td>
                <td class='small'>".htmlentities($row['start_date'], ENT_QUOTES, 'UTF-8')."</td>
                <td class='small'>".htmlentities($row['expected_end_date'], ENT_QUOTES, 'UTF-8')."</td>
                <td>{$imgTag}</td>
                <td><a class='btn btn-danger' href='delete.php?table=ongoing_projects&id={$id}' onclick=\"return confirm('Delete this ongoing project?');\">Delete</a></td>
                </tr>";
            }
          } else {
            echo "<tr><td colspan='6'>No ongoing projects found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <!-- ================= Reset Password ================= -->
    <section id="reset" class="card">
      <h2>Reset Admin Password</h2>
      <form action="reset_password.php" method="POST">
        <input type="email" name="email" placeholder="Enter Admin Email" required>
        <div class="password-field">
          <input type="password" name="new_password" placeholder="New Password" required id="new_password">
          <i class="fa fa-eye" id="toggleNew"></i>
        </div>
        <div class="password-field">
          <input type="password" name="confirm_password" placeholder="Confirm New Password" required id="confirm_password">
          <i class="fa fa-eye" id="toggleConfirm"></i>
        </div>
        <button class="btn btn-primary" type="submit">Reset Password</button>
      </form>
    </section>
  </main>

  <script>
    // Logout button redirects to login page
    document.getElementById("logoutBtn").addEventListener("click", ()=>{
      window.location.href='login.php';
    });

    // Password toggle for New Password
    document.getElementById("toggleNew").addEventListener("click", ()=>{
      const input = document.getElementById("new_password");
      input.type = input.type === "password" ? "text" : "password";
    });

    // Password toggle for Confirm Password
    document.getElementById("toggleConfirm").addEventListener("click", ()=>{
      const input = document.getElementById("confirm_password");
      input.type = input.type === "password" ? "text" : "password";
    });
  </script>
</body>
</html>
