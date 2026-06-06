<?php
header("Content-Type: application/json");
require_once 'db1.php';

// Only allow GET requests to safely pull data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, company_name, contact_person, email, category FROM vendors ORDER BY id DESC";
    $result = $conn->query($sql);
    
    $vendors = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $vendors[] = $row;
        }
    }
    
    // Output the exact JSON format your teammate's JavaScript loops through
    echo json_encode($vendors);
    exit;
}

$conn->close();
?>