<?php
// backend/update_rfq_status.php
require_once 'db.php';
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $stmt = $pdo->prepare("UPDATE rfqs SET status = 'sent' WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>