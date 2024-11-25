<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start(); // Clear any accidental output

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'forgotPassword';

    if ($action === 'forgotPassword') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';

        // Debug logging
        error_log("Forgot Password request: Email=$email");

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => "error",
                "message" => "Invalid or missing email address"
            ]);
            exit;
        }

        // Generate a new random password
        $newPassword = substr(md5(uniqid(rand(), true)), 0, 8);

        // Simulate sending the new password via email
        if (function_exists('mail')) {
            $mailSent = mail($email, "Password Reset", "Your new password is: $newPassword");

            if ($mailSent) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Password reset email sent",
                    "data" => [
                        "email" => $email,
                        "newPassword" => $newPassword
                    ]
                ]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to send password reset email. Please try again later."
                ]);
            }
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                "status" => "error",
                "message" => "mail() function not available. Check server configuration."
            ]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Invalid action specified"
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
ob_end_flush(); // Flush output buffer
?>
