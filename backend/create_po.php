<?php
session_start();
header("Content-Type: application/json");
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['quotation_id']) || !isset($data['vendor_id']) || !isset($data['total_amount'])) {
        echo json_encode(["status" => "error", "message" => "Missing parameters for PO generation"]);
        exit;
    }
    
    $quotation_id = intval($data['quotation_id']);
    $vendor_id = intval($data['vendor_id']);
    $total_amount = floatval($data['total_amount']);
    
    $po_no = "PO-" . rand(1000, 9999);
    $created_by = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 2; // Default to Aarav Patel from seed data
    
    $sql = "INSERT INTO purchase_orders (po_no, quotation_id, vendor_id, created_by, status, issue_date, total_amount) 
            VALUES ('$po_no', $quotation_id, $vendor_id, $created_by, 'generated', CURDATE(), $total_amount)";
            
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Purchase Order $po_no generated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "SQL PO Ledger Error: " . $conn->error]);
    }
    exit;
}
$conn->close();
?>