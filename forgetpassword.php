<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email from the POST request
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid or missing email address"
        ]);
        exit;
    }

    // Simulate success response
    echo json_encode([
        "status" => "success",
        "message" => "Password reset email sent successfully"
    ]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
