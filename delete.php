<?php
include 'db.php';

if(isset($_GET['table'], $_GET['id'])){
    $table = $_GET['table'];
    $id = (int)$_GET['id'];

    // Validate table name to prevent SQL injection
    $allowed_tables = ['contact_messages','career_applications','projects','ongoing_projects'];
    if(in_array($table, $allowed_tables)){
        $conn->query("DELETE FROM `$table` WHERE id=$id");
    }
}
header("Location: ".$_SERVER['HTTP_REFERER']);
exit;