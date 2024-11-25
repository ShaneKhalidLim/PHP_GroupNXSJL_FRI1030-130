<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Logging input for debugging
    error_log("Login attempt: Email=$email");

    // Validate inputs
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Invalid or missing email address"
        ]);
        exit;
    }

    if (empty($password) || strlen($password) < 6) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 6 characters long"
        ]);
        exit;
    }

    // Simulate a successful login response
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "data" => [
            "email" => $email
        ]
    ]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>
