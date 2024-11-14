<?php
header("Content-Type: application/json");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    $topic = isset($_POST['uploadTopic']) ? trim($_POST['uploadTopic']) : '';

    if (empty($topic)) {
        echo json_encode(["status" => "error", "message" => "Topic is required."]);
        exit;
    }

    // Check if an image file is uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "No image file uploaded."]);
        exit;
    }

    // File upload parameters
    $uploadDir = 'uploads/';
    $imageFile = $_FILES['image'];
    $imagePath = $uploadDir . uniqid() . basename($imageFile['name']);

    // Create the uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
        // Generate the URL of the uploaded image
        $imageURL = "http://" . $_SERVER['HTTP_HOST'] . "/" . $imagePath;

        // Return the image URL and topic in JSON format
        echo json_encode([
            "status" => "success",
            "message" => "Upload successful!",
            "imageURL" => $imageURL,
            "topic" => $topic
        ]);
    } else {
        // Error in moving the uploaded file
        echo json_encode(["status" => "error", "message" => "Failed to save the uploaded image."]);
    }
} else {
    // Invalid request method
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
