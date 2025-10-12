<?php
$servername = "localhost";   // XAMPP default
$username   = "root";        // XAMPP default
$password   = "";            // XAMPP default (empty password)
$dbname     = "bmace_admin"; // must match your CREATE DATABASE

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>