<?php
header("Content-Type: application/json");
require_once 'db1.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['rfq_id']) || !isset($data['vendor_id']) || !isset($data['grand_total'])) {
        echo json_encode(["status" => "error", "message" => "Missing proposal data variables"]);
        exit;
    }
    
    $rfq_id = intval($data['rfq_id']);
    $vendor_id = intval($data['vendor_id']);
    $grand_total = floatval($data['grand_total']);
    $delivery_timeline = $conn->real_escape_string($data['delivery_timeline']);
    $q_no = "QTN-" . rand(1000, 9999); // Auto-generate transactional serialization number
    
    $sql = "INSERT INTO quotations (quotation_no, rfq_id, vendor_id, grand_total, delivery_timeline, status) 
            VALUES ('$q_no', $rfq_id, $vendor_id, $grand_total, '$delivery_timeline', 'submitted')";
            
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Proposal filed successfully under $q_no"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database write failure execution: " . $conn->error]);
    }
    exit;
}
$conn->close();
?>