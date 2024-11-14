<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $topic = isset($_POST['uploadTopic']) ? trim($_POST['uploadTopic']) : '';

    if (empty($id) || empty($topic)) {
        echo json_encode(["status" => "error", "message" => "ID and topic are required."]);
        exit;
    }

    // Database path
    $dbPath = __DIR__ . '/souvseek.db';

    // Connect to SQLite database
    try {
        $db = new PDO("sqlite:$dbPath");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Database connection error: " . $e->getMessage()]);
        exit;
    }

    // Check if an image file is uploaded
    $imageURL = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to save the uploaded image."]);
            exit;
        }
    }

    // Perform the update in the database
    try {
        if ($imageURL) {
            $stmt = $db->prepare("UPDATE shop SET title = :title, image_url = :imageURL WHERE id = :id");
            $stmt->bindParam(':imageURL', $imageURL);
        } else {
            $stmt = $db->prepare("UPDATE shop SET title = :title WHERE id = :id");
        }

        $stmt->bindParam(':title', $topic);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Update successful!",
                "imageURL" => $imageURL,
                "topic" => $topic
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    // Invalid request method
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
