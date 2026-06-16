<?php
session_start();
header("Content-Type: application/json");
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['quotation_id']) || !isset($data['action'])) {
    echo json_encode(["status" => "error", "message" => "Missing required parameter arguments"]);
    exit;
}

$quotation_id = intval($data['quotation_id']);
$action = $data['action']; // Expecting 'approve' or 'reject'

// 1. Establish the string values based on your exact SQL schema ENUM choices
$quote_status = ($action === 'approve') ? 'shortlisted' : 'rejected';
$approval_decision = ($action === 'approve') ? 'approved' : 'rejected';

// 2. Identify who is executing this (Neha Shah is User ID 3 in your seed data)
$approver_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 3; 
$remarks = isset($data['remarks']) ? $conn->real_escape_string($data['remarks']) : 'Verified via rapid operational test node';

// A. Begin SQL Transaction to guarantee database safety across linked tables
$conn->begin_transaction();

try {
    // Update the Quotation row record status flag
    $conn->query("UPDATE quotations SET status='$quote_status' WHERE id=$quotation_id");

    // Insert or update the decision inside your explicit approvals audit table
    $conn->query("INSERT INTO approvals (quotation_id, approver_id, decision, remarks, decided_at) 
                  VALUES ($quotation_id, $approver_id, '$approval_decision', '$remarks', NOW())
                  ON DUPLICATE KEY UPDATE decision='$approval_decision', remarks='$remarks', decided_at=NOW()");

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Quotation processing complete. Decision: " . strtoupper($approval_decision)]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => "Transaction failure loop context: " . $e->getMessage()]);
}

$conn->close();
?>