<?php
header("Content-Type: application/json");

// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;

    // Log incoming inputs for debugging
    error_log("Received Email: $email");
    error_log("Received Password: $password");

    // Validate inputs
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Invalid or missing email address"
        ]);
        exit;
    }

    if (!$password || strlen($password) < 6) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 6 characters long"
        ]);
        exit;
    }

    // Simulate success response
    http_response_code(201); // Created
    echo json_encode([
        "status" => "success",
        "message" => "Request processed successfully",
        "data" => [
            "email" => $email
        ]
    ]);
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>
