<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../config/database.php';
require_once '../../utils/mailer.php';

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
        !isset($data['phone']) || empty(trim($data['phone'])) ||
        !isset($data['email']) || empty(trim($data['email'])) ||
        !isset($data['nrc_number']) || empty(trim($data['nrc_number']))) {
        throw new Exception('All fields are required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // Get current lead recruiter's information
    $leadRecruiterId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
    $stmt->execute([$leadRecruiterId]);
    $leadRecruiter = $stmt->fetch(PDO::FETCH_ASSOC);

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
       
        $query = "INSERT INTO users (firstname, lastname, email, nrc_number, phone_number, role_id, status) 
                 VALUES (:firstname, :lastname, :email, :nrc_number, :phone_number, :role_id, :status)";
        $stmt = $conn->prepare($query);
        
        $message = 'Recruiter added successfully';
        $isNewRecruiter = true;
    } else {
        // Update existing recruiter
        $query = "UPDATE users SET 
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    nrc_number = :nrc_number,
                    phone_number = :phone_number,
                    status = :status";
    
        $query .= " WHERE id = :id";
        $stmt = $conn->prepare($query);
        
       
        $stmt->bindParam(':id', $data['id']);
        
        $message = 'Recruiter updated successfully';
        $isNewRecruiter = false;
    }

    // Bind common parameters
    $stmt->bindParam(':firstname', $data['firstname']);
    $stmt->bindParam(':lastname', $data['lastname']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':nrc_number', $data['nrc_number']);
    $stmt->bindParam(':phone_number', $data['phone']);
    $stmt->bindParam(':status', $data['status']);
    if ($isNewRecruiter) {
        $stmt->bindParam(':role_id', $roleId);
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to save recruiter');
    }

    // Send welcome email for new recruiters
    if ($isNewRecruiter) {
        $mailer = new Mailer();
        $recruiterName = $data['firstname'] . ' ' . $data['lastname'];
        $leadRecruiterName = $leadRecruiter['firstname'] . ' ' . $leadRecruiter['lastname'];
        
        $emailSent = $mailer->sendWelcomeEmail(
            $data['email'],
            $recruiterName,
            $leadRecruiterName,
            $leadRecruiter['email']
        );

        if (!$emailSent) {
            // Log the error but don't stop the transaction
            error_log("Failed to send welcome email to: " . $data['email']);
        }
    }

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => $message . ($isNewRecruiter ? '. Welcome email has been sent to the recruiter.' : '')
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