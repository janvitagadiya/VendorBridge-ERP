<?php
header("Content-Type: application/json");
require_once 'db.php';

try {
    // 1. Pending Approvals
   $pending = $pdo->query("SELECT COUNT(*) FROM approvals WHERE decision = 'pending'")->fetchColumn();

    // 2. Monthly PO Amount
    $po_amount = $pdo->query("SELECT SUM(total_amount) FROM purchase_orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn();

    // 3. Overdue Invoices
    $overdue = $pdo->query("SELECT COUNT(*) FROM invoices WHERE due_date < CURRENT_DATE() AND status != 'paid'")->fetchColumn();

    // 4. RFQ Count (Counting drafts as active)
   // Count everything that isn't finished or canceled
// backend/get_dashboard_stats.php

// This counts anything that isn't finished (closed) or cancelled
$stmt = $pdo->query("SELECT COUNT(*) FROM rfqs WHERE status IN ('draft', 'sent')");
$rfq_count = $stmt->fetchColumn();
    // 5. Spending Trends
    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%b %Y') as month, SUM(total_amount) as total 
                         FROM purchase_orders 
                         GROUP BY MONTH(created_at) ORDER BY created_at DESC LIMIT 6");
    $trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "pending_approvals" => $pending,
        "monthly_po_amount" => $po_amount ?: 0,
        "overdue_invoices" => $overdue,
        "rfq_count" => $rfq_count, // Added this
        "spending_trends" => array_reverse($trends)
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>