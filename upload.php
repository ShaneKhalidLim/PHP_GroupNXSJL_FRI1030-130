<?php
header("Content-Type: application/json");

// Allow CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    $topic = isset($_POST['uploadTopic']) ? trim($_POST['uploadTopic']) : '';

    if (empty($topic)) {
        echo json_encode(["status" => "error", "message" => "Topic is required."]);
        exit;
    }

    // Check if an image file is uploaded
    if (!isset($_FILES['image'])) {
        echo json_encode(["status" => "error", "message" => "No file uploaded."]);
        exit;
    }

    // Check for file upload errors
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "File upload stopped by a PHP extension."
        ];

        $errorMessage = $errorMessages[$_FILES['image']['error']] ?? "Unknown upload error.";
        echo json_encode(["status" => "error", "message" => $errorMessage]);
        exit;
    }

    // File upload parameters
    $uploadDir = 'uploads/';
    $imageFile = $_FILES['image'];

    // Generate a unique file name to prevent overwrites
    $imagePath = $uploadDir . uniqid() . "_" . basename($imageFile['name']);

    // Create the uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            echo json_encode(["status" => "error", "message" => "Failed to create uploads directory."]);
            exit;
        }
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
