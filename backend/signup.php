<?php
error_reporting(0);
header("Content-Type: application/json");
require_once 'db.php';
$data = json_decode(file_get_contents("php://input"), true);

// 1. Validate incoming data
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Missing email or password"]);
    exit;
}

// 2. Hash
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// 3. Insert with explicit column list
// We exclude 'id', 'created_at', and 'updated_at' because the DB handles those
try {
    $sql = "INSERT INTO users (role_id, name, email, password_hash, phone, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // We pass '1' for role_id (admin), and NULL for phone
    $stmt->execute([
        1, 
        $data['name'], 
        $data['email'], 
        $hashedPassword, 
        NULL, 
        'active'
    ]);
    
    echo json_encode(["status" => "success", "message" => "Account created successfully!"]);
} catch (PDOException $e) {
    // This will print the exact SQL error
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>