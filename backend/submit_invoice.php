<?php
header("Content-Type: application/json");
require_once 'db1.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['purchase_order_id']) || !isset($data['vendor_id']) || !isset($data['total_amount'])) {
        echo json_encode(["status" => "error", "message" => "Incomplete Invoice data fields"]);
        exit;
    }
    
    $purchase_order_id = intval($data['purchase_order_id']);
    $vendor_id = intval($data['vendor_id']);
    $total_amount = floatval($data['total_amount']);
    
    $invoice_no = "INV-" . rand(10000, 99999);
    
    $sql = "INSERT INTO invoices (invoice_no, purchase_order_id, vendor_id, invoice_date, due_date, total_amount, status) 
            VALUES ('$invoice_no', $purchase_order_id, $vendor_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), $total_amount, 'issued')";
            
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Invoice $invoice_no logged successfully! Terms: Net 30."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invoice Creation Failure: " . $conn->error]);
    }
    exit;
}
$conn->close();
?>