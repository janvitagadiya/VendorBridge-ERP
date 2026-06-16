<?php
error_reporting(0);
header("Content-Type: application/json");
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// 1. Fetch user AND the role name in one go
$stmt = $pdo->prepare("SELECT u.*, r.name as role_name 
                       FROM users u 
                       JOIN roles r ON u.role_id = r.id 
                       WHERE u.email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["status" => "error", "message" => "DEBUG: No user exists with email " . $data['email']]);
} elseif ($user['role_name'] !== $data['role']) {
    echo json_encode(["status" => "error", "message" => "DEBUG: Role mismatch. User is " . $user['role_name'] . " but you selected " . $data['role']]);
} elseif (!password_verify($data['password'], $user['password_hash'])) {
    echo json_encode(["status" => "error", "message" => "DEBUG: Incorrect password."]);
} else {
   // ... inside your successful login block
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role_name'];
$_SESSION['role_id'] = $user['role_id'];
$_SESSION['user_name'] = $user['name']; // Add this line
echo json_encode(["status" => "success", "message" => "Login successful"]);
}
?>