<?php
header("Content-Type: application/json");
require_once 'db1.php';

// HANDLE SAVING NEW INVOICES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['purchase_order_id']) || !isset($data['vendor_id']) || !isset($data['total_amount'])) {
        echo json_encode(["status" => "error", "message" => "Missing required relational schema parameters"]);
        exit;
    }
    
    $invoice_no = "INV-" . rand(10000, 99999);
    $purchase_order_id = intval($data['purchase_order_id']);
    $vendor_id = intval($data['vendor_id']);
    $total_amount = floatval($data['total_amount']);
    
    // Aligned 100% to your schema columns: mapping keys and setting due date 30 days out
    $sql = "INSERT INTO invoices (invoice_no, purchase_order_id, vendor_id, invoice_date, due_date, total_amount, status) 
            VALUES ('$invoice_no', $purchase_order_id, $vendor_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), $total_amount, 'issued')";
            
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Invoice $invoice_no committed successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "SQL Execution Crash: " . $conn->error]);
    }
    exit;
}

// HANDLE FETCHING INVOICES FOR LEDGER DISPLAY
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Relational join to pull the human-readable text strings for your frontend display
    $sql = "SELECT i.invoice_no, i.invoice_date, i.due_date, i.total_amount, i.status, po.po_no, v.company_name 
            FROM invoices i
            JOIN purchase_orders po ON i.purchase_order_id = po.id
            JOIN vendors v ON i.vendor_id = v.id
            ORDER BY i.id DESC";
            
    $result = $conn->query($sql);
    $invoices = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $invoices[] = $row;
        }
    }
    echo json_encode($invoices);
    exit;
}
$conn->close();
?>