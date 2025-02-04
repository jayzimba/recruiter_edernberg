<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../config/database.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || empty(trim($data['name']))) {
        throw new Exception('School name is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    if (empty($data['id'])) {
        // Insert new school
        $query = "INSERT INTO schools (school_name) VALUES (:name)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);

        if (!$stmt->execute()) {
            throw new Exception('Failed to create school');
        }

        $message = 'School created successfully';
    } else {
        // Update existing school
        $query = "UPDATE schools SET school_name = :name WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id', $data['id']);

        if (!$stmt->execute()) {
            throw new Exception('Failed to update school');
        }

        $message = 'School updated successfully';
    }

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => $message
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Error saving school: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
