<?php
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Login Required"
    ]);
    exit;
}

if ($_SESSION['role_id'] != 4) {
    echo json_encode([
        "status" => "error",
        "message" => "Access Denied"
    ]);
    exit;
}

header("Content-Type: application/json");
require_once 'db.php'; // Uses $pdo

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $rfq_id = intval($data['rfq_id'] ?? 0);
        $vendor_id = intval($data['vendor_id'] ?? 0); // SET TO YOUR CORRECT ID
        
        $qty = floatval($data['quantity'] ?? 0);
        $unit_price = floatval($data['grand_total'] ?? 0);
        $delivery = $data['delivery_timeline'] ?? null;
        $total = $qty * $unit_price;
        $q_no = "QTN-" . rand(1000, 9999);
        
        // 1. Insert Header
        $stmt = $pdo->prepare("INSERT INTO quotations (quotation_no, rfq_id, vendor_id, delivery_timeline, subtotal, grand_total, status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'submitted')");
        $stmt->execute([$q_no, $rfq_id, $vendor_id, $delivery, $total, $total]);
        
        $quotation_id = $pdo->lastInsertId();
        
        // 2. Insert Item
      $rfq_item_id = intval($data['rfq_item_id'] ?? 0);

$stmtItem = $pdo->prepare("
INSERT INTO quotation_items
(quotation_id, rfq_item_id, unit_price, quantity, total_price)
VALUES (?, ?, ?, ?, ?)
");

$stmtItem->execute([
    $quotation_id,
    $rfq_item_id,
    $unit_price,
    $qty,
    $total
]);
        
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Proposal $q_no submitted successfully"]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Database failure: " . $e->getMessage()]);
    }
    exit;
}
?>