<?php
// =======================
// BMACE Database Connection
// =======================

$servername = "sql210.infinityfree.com";
$username   = "if0_40228950";
$password   = "371248Isaac";  // your InfinityFree DB password
$dbname     = "if0_40228950_bmace_admin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}

// Optional message (for quick tests)
// echo "✅ Database connection successful!";
?>
