<?php
require_once '../../config/database.php';
require_once '../../middleware/check_auth.php';

header('Content-Type: application/json');

// Ensure user is authenticated as a recruiter
checkAuth(['recruiter']);

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Validate required fields
        $required_fields = ['application_id', 'firstname', 'lastname', 'email', 'contact', 'G_ID', 'nationality'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Update the application
        $query = "UPDATE students 
                 SET firstname = :firstname,
                     middlename = :middlename,
                     lastname = :lastname,
                     email = :email,
                     contact = :contact,
                     G_ID = :G_ID,
                     nationality = :nationality,
                     updated_at = NOW()
                 WHERE id = :application_id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':firstname', $data['firstname']);
        $stmt->bindParam(':middlename', $data['middlename']);
        $stmt->bindParam(':lastname', $data['lastname']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':contact', $data['contact']);
        $stmt->bindParam(':G_ID', $data['G_ID']);
        $stmt->bindParam(':nationality', $data['nationality']);
        $stmt->bindParam(':application_id', $data['application_id']);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => true,
                'message' => 'Information updated successfully'
            ]);
        } else {
            throw new Exception("Failed to update information");
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request method or data'
    ]);
} 