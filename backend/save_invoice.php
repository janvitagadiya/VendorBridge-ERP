<?php

header("Content-Type: application/json");
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !isset($data['purchase_order_id']) ||
        !isset($data['vendor_id']) ||
        !isset($data['total_amount'])
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields"
        ]);
        exit;
    }

    $purchase_order_id = $data['purchase_order_id'];

    // Prevent duplicate invoice for same PO
    $stmt = $pdo->prepare("
        SELECT id
        FROM invoices
        WHERE purchase_order_id = ?
    ");

    $stmt->execute([$purchase_order_id]);

    if ($stmt->fetch()) {

        echo json_encode([
            "status" => "error",
            "message" => "Invoice already exists for this Purchase Order"
        ]);
        exit;
    }

    $invoice_no = 'INV-' . date('Y') . '-' . time();

    $stmt = $pdo->prepare("
        INSERT INTO invoices
        (
            invoice_no,
            purchase_order_id,
            vendor_id,
            invoice_date,
            due_date,
            total_amount,
            status
        )
        VALUES
        (
            ?, ?, ?, CURDATE(),
            DATE_ADD(CURDATE(), INTERVAL 30 DAY),
            ?, 'issued'
        )
    ");

    $stmt->execute([
        $invoice_no,
        $data['purchase_order_id'],
        $data['vendor_id'],
        $data['total_amount']
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Invoice generated successfully"
    ]);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $stmt = $pdo->query("
        SELECT
            i.invoice_no,
            i.invoice_date,
            i.due_date,
            i.total_amount,
            i.status,
            po.po_no,
            v.company_name
        FROM invoices i
        JOIN purchase_orders po
            ON i.purchase_order_id = po.id
        JOIN vendors v
            ON i.vendor_id = v.id
        ORDER BY i.id DESC
    ");

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );

    exit;
}
?>