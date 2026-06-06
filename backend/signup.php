<?php
header("Content-Type: application/json");
require_once 'db1.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
    echo json_encode(["status" => "error", "message" => "Incomplete parameter structure"]);
    exit;
}

$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$incoming_role = $data['role'];
$hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

// Map the form inputs to match your exact database seeded roles
$role_map = [
    "officer" => "procurement_officer",
    "manager" => "manager",
    "vendor"  => "vendor"
];

$role_name = isset($role_map[$incoming_role]) ? $role_map[$incoming_role] : $incoming_role;
$role_name = $conn->real_escape_string($role_name);

// Find corresponding role_id from your roles table
$role_res = $conn->query("SELECT id FROM roles WHERE name='$role_name' LIMIT 1");
if (!$role_res || $role_res->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "System role context does not exist"]);
    exit;
}
$role_row = $role_res->fetch_assoc();
$role_id = $role_row['id'];

// Check for duplicates
$check = $conn->query("SELECT id FROM users WHERE email='$email' LIMIT 1");
if ($check && $check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email profile is already onboarded"]);
    exit;
}

$sql = "INSERT INTO users (role_id, name, email, password_hash, status) 
        VALUES ($role_id, '$name', '$email', '$hashed_password', 'active')";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Account registration successful"]);
} else {
    echo json_encode(["status" => "error", "message" => "Insertion exception: " . $conn->error]);
}

$conn->close();
?>