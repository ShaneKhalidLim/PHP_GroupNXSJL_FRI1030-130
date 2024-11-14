<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        echo json_encode([
            "status" => "success",
            "message" => "User registered successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email and password are required"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>
