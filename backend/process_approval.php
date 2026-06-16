<?php

session_start();
require_once 'db.php';

$approval_id = $_POST['approval_id'];
$quotation_id = $_POST['quotation_id'];
$decision = $_POST['action'];

try {

    $pdo->beginTransaction();

    // Update approval decision
    $stmt = $pdo->prepare("
        UPDATE approvals
        SET decision = ?,
            decided_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $decision,
        $approval_id
    ]);

    // If approved, generate Purchase Order
    if ($decision == 'approved') {

        // Fetch quotation details
        $stmt = $pdo->prepare("
            SELECT
                q.id,
                q.vendor_id,
                q.grand_total
            FROM quotations q
            WHERE q.id = ?
        ");

        $stmt->execute([$quotation_id]);

        $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$quotation) {
            throw new Exception("Quotation not found");
        }

        // Generate PO Number
        $po_no = 'PO-' . date('Y') . '-' . time();

        // Insert Purchase Order
        $stmt = $pdo->prepare("
            INSERT INTO purchase_orders
            (
                po_no,
                quotation_id,
                vendor_id,
                created_by,
                status,
                issue_date,
                total_amount
            )
            VALUES
            (
                ?, ?, ?, ?, 'generated', CURDATE(), ?
            )
        ");

        $stmt->execute([
            $po_no,
            $quotation_id,
            $quotation['vendor_id'],
            $_SESSION['user_id'],
            $quotation['grand_total']
        ]);
    }

    $pdo->commit();

    header("Location: approvals.php");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    die($e->getMessage());

}
?>