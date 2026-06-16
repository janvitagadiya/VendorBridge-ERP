<?php
header("Content-Type: application/json");
require_once 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "No RFQ ID provided"]);
    exit;
}

// 1. Get the RFQ header info
$stmt = $pdo->prepare("SELECT * FROM rfqs WHERE id = ?");
$stmt->execute([$id]);
$rfq = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rfq) {
    echo json_encode(["status" => "error", "message" => "RFQ not found"]);
    exit;
}

// 2. Get the line items for this specific RFQ
$stmtItems = $pdo->prepare("SELECT * FROM rfq_items WHERE rfq_id = ?");
$stmtItems->execute([$id]);
$rfq['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rfq);
?>