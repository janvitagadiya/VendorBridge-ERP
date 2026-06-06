<?php
header("Content-Type: application/json");
require_once 'db1.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, rfq_no, title, deadline, status FROM rfqs ORDER BY id DESC";
    $result = $conn->query($sql);
    
    $rfqs = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rfqs[] = $row;
        }
    }
    
    // Output ONLY the raw array list
    echo json_encode($rfqs);
    exit;
}
$conn->close();
?>