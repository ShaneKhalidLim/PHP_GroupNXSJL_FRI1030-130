<?php
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

// Path to SQLite database
$dbPath = __DIR__ . '/souvseek.db';

// Check if the database file exists
if (!file_exists($dbPath)) {
    echo json_encode(["status" => "error", "message" => "Database file not found."]);
    exit;
}

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

switch ($method) {
    case 'GET':
        // Fetch all shop items
        try {
            $stmt = $db->query("SELECT * FROM shop");
            $shopItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Always output an empty array if no items are found
            if (!$shopItems) {
                $shopItems = [];
            }

            echo json_encode($shopItems); // Ensure this outputs an array
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Failed to retrieve items: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        // Insert a new shop item
        $topic = $_POST['uploadTopic'] ?? '';

        if (empty($topic)) {
            echo json_encode(["status" => "error", "message" => "Topic is required."]);
            exit;
        }

        // Validate if an image file was uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["status" => "error", "message" => "No image file uploaded."]);
            exit;
        }

        // Set up the upload directory
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            echo json_encode(["status" => "error", "message" => "Failed to create upload directory."]);
            exit;
        }

        $imageFile = $_FILES['image'];
        $imagePath = $uploadDir . uniqid() . basename($imageFile['name']);
        
        // Move the uploaded image file
        if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $imageUrl = "http://" . $_SERVER['HTTP_HOST'] . "/" . $imagePath;

            try {
                // Insert the new shop item into the database
                $stmt = $db->prepare("INSERT INTO shop (title, image_url) VALUES (:title, :image_url)");
                $stmt->bindParam(':title', $topic);
                $stmt->bindParam(':image_url', $imageUrl);
                $stmt->execute();

                echo json_encode(["status" => "success", "message" => "Upload successful!", "imageURL" => $imageUrl]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Failed to save item: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to save the uploaded image."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method."]);
        break;
}

?>
