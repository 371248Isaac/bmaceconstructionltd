<?php
// Generic upload handler
function uploadFile($fileInput, $targetDir = "uploads/") {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return [false, "No file uploaded."];
    }

    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $filename = time() . "_" . basename($_FILES[$fileInput]['name']);
    $targetPath = $targetDir . $filename;

    if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $targetPath)) {
        return [true, $targetPath];
    } else {
        return [false, "Failed to upload file."];
    }
}
?>