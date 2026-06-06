<?php
header("Content-Type: application/json");
require_once 'db1.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT id, company_name, contact_person, email, category FROM vendors ORDER BY id DESC";
    $result = $conn->query($sql);
    
    $vendors = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $vendors[] = $row;
        }
    }
    echo json_encode($vendors);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['company_name']) || !isset($data['contact_person']) || !isset($data['email'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }
    
    $company_name = $conn->real_escape_string($data['company_name']);
    $contact_person = $conn->real_escape_string($data['contact_person']);
    $email = $conn->real_escape_string($data['email']);
    $category = isset($data['category']) ? $conn->real_escape_string($data['category']) : 'Supplier';
    
    $sql = "INSERT INTO vendors (company_name, contact_person, email, category) VALUES ('$company_name', '$contact_person', '$email', '$category')";
    
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Vendor successfully logged"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    }
    exit;
}

$conn->close();
?>