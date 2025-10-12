<?php
require('fpdf/fpdf.php');
include 'db.php';

if (!isset($_GET['table'])) {
    die("❌ Table not specified.");
}

$table = $_GET['table'];
$allowedTables = ["contact_messages", "career_applications", "projects", "ongoing_projects"];

if (!in_array($table, $allowedTables)) {
    die("❌ Invalid table requested.");
}

$result = $conn->query("SELECT * FROM $table");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'BMACE CONSTRUCTION LTD - '.strtoupper($_GET['table']).' REPORT',0,1,'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

// Column headers
$fields = $result->fetch_fields();
foreach ($fields as $field) {
    $pdf->Cell(40,10,ucfirst($field->name),1);
}
$pdf->Ln();

// Data rows
$pdf->SetFont('Arial','',9);
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    foreach ($row as $col) {
        $pdf->Cell(40,10,substr($col,0,20),1);
    }
    $pdf->Ln();
}

$pdf->Output();
?>