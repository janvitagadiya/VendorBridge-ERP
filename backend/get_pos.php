<?php
header("Content-Type: application/json");
require_once 'db1.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT po.id, po.po_no, po.total_amount, po.status, po.vendor_id, v.company_name 
            FROM purchase_orders po 
            JOIN vendors v ON po.vendor_id = v.id 
            ORDER BY po.id DESC";
            
    $result = $conn->query($sql);
    $pos = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pos[] = $row;
        }
    }
    echo json_encode($pos);
    exit;
}
$conn->close();
?>