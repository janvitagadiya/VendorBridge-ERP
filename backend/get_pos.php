<?php

header("Content-Type: application/json");
require_once 'db.php';

$stmt = $pdo->query("
SELECT
    po.id,
    po.po_no,
    po.total_amount,
    po.status,
    po.vendor_id,
    v.company_name
FROM purchase_orders po
JOIN vendors v
    ON po.vendor_id = v.id
ORDER BY po.id DESC
");

$purchaseOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($purchaseOrders);

?>