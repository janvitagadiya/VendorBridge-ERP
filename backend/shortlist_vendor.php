<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quotation_id = $_POST['quotation_id'];
    $rfq_id = $_POST['rfq_id'];

    try {

        $pdo->beginTransaction();

        // Reject all quotations for this RFQ
        $stmt = $pdo->prepare("
            UPDATE quotations
            SET status='rejected'
            WHERE rfq_id=?
        ");
        $stmt->execute([$rfq_id]);

        // Shortlist selected quotation
        $stmt = $pdo->prepare("
            UPDATE quotations
            SET status='shortlisted'
            WHERE id=?
        ");
        $stmt->execute([$quotation_id]);

        // Create approval request
        $stmt = $pdo->prepare("
            INSERT INTO approvals
            (quotation_id, approver_id, decision)
            VALUES (?, ?, 'pending')
        ");

        // Use any test manager ID for now
        $stmt->execute([$quotation_id, 5]);

        $pdo->commit();

        header("Location: comparison.php?rfq_id=".$rfq_id);
        exit;

    } catch(Exception $e) {

        $pdo->rollBack();
        die($e->getMessage());

    }
}
?>