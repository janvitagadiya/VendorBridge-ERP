<?php
require_once 'db.php';
$stmt = $pdo->query("SELECT * FROM rfqs ORDER BY created_at DESC");
$rfqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rfqs);
?>