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
    if (!isset($data['firstname']) || empty(trim($data['firstname'])) ||
        !isset($data['lastname']) || empty(trim($data['lastname'])) ||
        !isset($data['email']) || empty(trim($data['email'])) ||
        !isset($data['nrc_number']) || empty(trim($data['nrc_number']))) {
        throw new Exception('All fields are required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // Get recruiter role ID
    $query = "SELECT id FROM user_roles WHERE name = 'recruiter'";
    $stmt = $conn->query($query);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    $roleId = $role['id'];

    if (empty($data['id'])) {
        // Check if email or NRC already exists when creating new recruiter
        $query = "SELECT id FROM users WHERE email = :email OR nrc_number = :nrc_number";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':nrc_number', $data['nrc_number']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            throw new Exception('Email or NRC number already exists');
        }
       
        $query = "INSERT INTO users (firstname, lastname, email, nrc_number, role_id, status) 
                 VALUES (:firstname, :lastname, :email, :nrc_number, :role_id, :status)";
        $stmt = $conn->prepare($query);
        
        $message = 'Recruiter added successfully';
    } else {
        // Update existing recruiter
        $query = "UPDATE users SET 
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    nrc_number = :nrc_number,
                    status = :status";
        
      
        $query .= " WHERE id = :id";
        $stmt = $conn->prepare($query);
        
       
        $stmt->bindParam(':id', $data['id']);
        
        $message = 'Recruiter updated successfully';
    }

    // Bind common parameters
    $stmt->bindParam(':firstname', $data['firstname']);
    $stmt->bindParam(':lastname', $data['lastname']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':nrc_number', $data['nrc_number']);
    $stmt->bindParam(':status', $data['status']);
    if (empty($data['id'])) {
        $stmt->bindParam(':role_id', $roleId);
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to save recruiter');
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
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 