<?php
session_start();
error_reporting(E_ALL);
require_once 'db.php';
header("Content-Type: application/json");
if ($_SESSION['role_id'] != 2) {
    echo json_encode([
        "status" => "error",
        "message" => "Access Denied"
    ]);
    exit;
}


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate required header fields
    if (!isset($data['rfq_no'], $data['title'], $data['deadline'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }
    
    
    $created_by = $_SESSION['user_id'] ?? 1;
    
    try {
        $pdo->beginTransaction();
        
        // 1. Insert RFQ Header
        $sql = "INSERT INTO rfqs (rfq_no, title, created_by, deadline, status, notes) VALUES (?, ?, ?, ?, 'draft', ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$data['rfq_no'], $data['title'], $created_by, $data['deadline'], $data['notes'] ?? '']);
        $rfq_id = $pdo->lastInsertId();
        
        // 2. Insert Line Items
        if (!empty($data['items'])) {
            $itemSql = "INSERT INTO rfq_items (rfq_id, item_name, quantity, unit) VALUES (?, ?, ?, ?)";
            $itemStmt = $pdo->prepare($itemSql);
            foreach ($data['items'] as $item) {
                $itemStmt->execute([$rfq_id, $item['item_name'], $item['quantity'], $item['unit']]);
            }
        }
        
        // 3. Insert Vendor Assignments
        if (!empty($data['vendors'])) {
            $vendSql = "INSERT INTO rfq_vendor_assignments (rfq_id, vendor_id, invitation_status) VALUES (?, ?, 'pending')";
            $vendStmt = $pdo->prepare($vendSql);
            foreach ($data['vendors'] as $vendor_id) {
                $vendStmt->execute([$rfq_id, $vendor_id]);
            }
        }
        
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "RFQ created successfully"]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}
?>