<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Log incoming request data for debugging
error_log("POST Data: " . print_r($_POST, true));
error_log("FILES Data: " . print_r($_FILES, true));

// Validate the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : ''; // Use the provided ID
    $title = isset($_POST['uploadTopic']) ? trim($_POST['uploadTopic']) : ''; // Use 'uploadTopic'

    // Check for missing required fields
    if (empty($id) || empty($title)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID and title are required."]);
        exit;
    }

    // Optional: Initialize a variable to hold the uploaded image URL
    $imageURL = null;

    // Check if an image is being uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        $imageFile = $_FILES['image'];
        $uniqueFileName = uniqid() . '_' . basename($imageFile['name']);
        $imagePath = $uploadDir . $uniqueFileName;

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $imageURL = "http://" . $_SERVER['HTTP_HOST'] . "/uploads/" . $uniqueFileName;
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Image upload failed."]);
            exit;
        }
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Data updated successfully.",
        "data" => [
            "id" => $id,
            "title" => $title,
            "imageURL" => $imageURL // Include the image URL only if provided
        ]
    ]);
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
