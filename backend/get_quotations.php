<?php
header("Content-Type: application/json");
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Left Join queries to dynamically fetch company metadata text strings 
    $sql = "SELECT q.id, q.quotation_no, q.grand_total, q.delivery_timeline, q.status, v.company_name 
            FROM quotations q 
            JOIN vendors v ON q.vendor_id = v.id 
            ORDER BY q.id DESC";
            
    $result = $conn->query($sql);
    $quotes = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quotes[] = $row;
        }
    }
    echo json_encode($quotes);
    exit;
}
$conn->close();
?>