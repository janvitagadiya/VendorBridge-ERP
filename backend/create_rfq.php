<?php
session_start();
header("Content-Type: application/json");
require_once 'db1.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT id, rfq_no, title, deadline, status FROM rfqs ORDER BY id DESC";
    $result = $conn->query($sql);
    
    $rfqs = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rfqs[] = $row;
        }
    }
    echo json_encode($rfqs);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['rfq_no']) || !isset($data['title']) || !isset($data['deadline'])) {
        echo json_encode(["status" => "error", "message" => "Missing required RFQ fields"]);
        exit;
    }
    
    $rfq_no = $conn->real_escape_string($data['rfq_no']);
    $title = $conn->real_escape_string($data['title']);
    $deadline = $conn->real_escape_string($data['deadline']);
    $notes = isset($data['notes']) ? $conn->real_escape_string($data['notes']) : '';
    
    // Assign created_by using the logged-in session ID, or fallback to 1 if testing without login
    $created_by = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
    
    // Maps seamlessly to your table setup: default status value is set to 'draft'
    $sql = "INSERT INTO rfqs (rfq_no, title, created_by, deadline, status, notes) 
            VALUES ('$rfq_no', '$title', $created_by, '$deadline', 'draft', '$notes')";
    
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "RFQ record created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Could not write to database: " . $conn->error]);
    }
    exit;
}

$conn->close();
?>