<?php
// Start the session to access the variables stored during login
session_start();

// Tell the browser we are sending JSON data
header("Content-Type: application/json");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, return Guest status
    echo json_encode(["user_name" => "Guest", "role" => "Unauthenticated"]);
    exit;
}

// Return the user data stored in the session
echo json_encode([
    "user_name" => $_SESSION['user_name'],
    "role" => $_SESSION['role']
]);
?>