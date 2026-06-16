<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Login Required");
}

if ($_SESSION['role_id'] != 4) {
    die("Access Denied");
}
?>
<?php


require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $sql = "SELECT id, company_name, contact_person, email, category
            FROM vendors
            ORDER BY id DESC";

    $stmt = $pdo->query($sql);

    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($vendors);
    exit;
}

if ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !isset($data['company_name']) ||
        !isset($data['contact_person']) ||
        !isset($data['email'])
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields"
        ]);
        exit;
    }

    $sql = "INSERT INTO vendors
            (company_name, contact_person, email, category)
            VALUES (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $category = $data['category'] ?? 'Supplier';

    if ($stmt->execute([
        $data['company_name'],
        $data['contact_person'],
        $data['email'],
        $category
    ])) {

        echo json_encode([
            "status" => "success",
            "message" => "Vendor added successfully"
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }

    exit;
}
?>