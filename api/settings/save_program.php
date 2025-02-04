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

    // Validate required fields
    if (!isset($data['name']) || empty(trim($data['name']))) {
        throw new Exception('Program name is required');
    }
    if (!isset($data['school_id']) || empty($data['school_id'])) {
        throw new Exception('School is required');
    }
    if (!isset($data['duration']) || empty($data['duration'])) {
        throw new Exception('Duration is required');
    }
    if (!isset($data['tuition_fee']) || empty($data['tuition_fee'])) {
        throw new Exception('Tuition fee is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    if (empty($data['id'])) {
        // Insert new program
        $query = "INSERT INTO programs (program_name, school_id, level_id, duration, tuition_fee) 
                 VALUES (:name, :school_id, :level_id, :duration, :tuition_fee)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':school_id', $data['school_id']);
        $stmt->bindParam(':level_id', $data['level_id']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':tuition_fee', $data['tuition_fee']);

        if (!$stmt->execute()) {
            throw new Exception('Failed to create program');
        }

        $message = 'Program created successfully';
    } else {
        // Update existing program
        $query = "UPDATE programs 
                 SET program_name = :name, 
                     school_id = :school_id, 
                     duration = :duration, 
                     tuition_fee = :tuition_fee 
                 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':school_id', $data['school_id']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':tuition_fee', $data['tuition_fee']);
        $stmt->bindParam(':id', $data['id']);

        if (!$stmt->execute()) {
            throw new Exception('Failed to update program');
        }

        $message = 'Program updated successfully';
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
    error_log("Error saving program: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
