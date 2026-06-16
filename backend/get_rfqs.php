
<?php
header("Content-Type: application/json");
require_once 'db.php';

// Fetch all RFQs that are currently sent to vendors
// Modify the WHERE clause to include 'draft'
$stmt = $pdo->query("SELECT id, rfq_no, title, deadline, status FROM rfqs WHERE status IN ('sent', 'draft') ORDER BY created_at DESC");
$rfqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rfqs);
?>