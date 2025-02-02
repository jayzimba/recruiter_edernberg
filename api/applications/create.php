<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$database = new Database();
$conn = $database->getConnection();

try {
    // Start transaction
    $conn->beginTransaction();

    // Validate required fields
    $required_fields = [
        'firstname',
        'lastname',
        'email',
        'contact',
        'nationality',
        'id_number',
        'program_id',
        'study_mode_id',
        'intake_id',
        'admission_id'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate session
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User session not found");
    }

    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'] ?? null;
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $nationality = $_POST['nationality'];
    $id_number = $_POST['id_number'];
    $program_id = $_POST['program_id'];
    $study_mode_id = $_POST['study_mode_id'];
    $intake_id = $_POST['intake_id'];
    $admission_id = $_POST['admission_id'];

    // Check if student already exists
    $checkQuery = "SELECT s.id, s.recruiter_id, u.firstname AS recruiter_name, u.lastname AS recruiter_lastname FROM students s 
                   JOIN users u ON s.recruiter_id = u.id
                   WHERE s.email = :email OR s.contact = :contact OR s.G_ID = :id_number";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->bindParam(':contact', $contact);
    $checkStmt->bindParam(':id_number', $id_number);
    $checkStmt->execute();

    if ($existingStudent = $checkStmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("Student is already onboarded by " . $existingStudent['recruiter_name'] . " " . $existingStudent['recruiter_lastname']);
    }


    // Insert student record
    $studentQuery = "INSERT INTO students (
        firstname, lastname, middlename, email, contact, nationality, 
        G_ID, program_id, study_mode_id, recruiter_id, intake_id, admission_type    
    ) VALUES (
        :firstname, :lastname, :middlename, :email, :contact, :nationality, 
        :id_number, :program_id, :study_mode_id, :recruiter_id, :intake_id, :admission_type
    )";

    $stmt = $conn->prepare($studentQuery);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':middlename', $middlename);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':nationality', $nationality);
    $stmt->bindParam(':id_number', $id_number);
    $stmt->bindParam(':program_id', $program_id);
    $stmt->bindParam(':study_mode_id', $study_mode_id);
    $stmt->bindParam(':intake_id', $intake_id);
    $stmt->bindParam(':admission_type', $admission_id);
    $stmt->bindParam(':recruiter_id', $_SESSION['user_id']);

    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception("Database error: " . $errorInfo[2]);
    }

    $student_id = $conn->lastInsertId();

    // Handle file uploads if available
    if (!empty($_FILES['attachment_file'])) {
        $uploadDir = __DIR__ . '/../../uploads/student_documents/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!is_writable($uploadDir)) {
            throw new Exception("Upload directory is not writable");
        }

        $attachmentQuery = "INSERT INTO attachments (student, type, uri, uploaded_by) 
                            VALUES (:student_id, :type, :uri, :uploaded_by)";
        $attachStmt = $conn->prepare($attachmentQuery);

        foreach ($_FILES['attachment_file']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['attachment_file']['error'][$index] === 0) {
                $document_type = $_POST['attachment_type'][$index];
                $extension = strtolower(pathinfo($_FILES['attachment_file']['name'][$index], PATHINFO_EXTENSION));
                $filename = $document_type . '_' . uniqid() . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($tmpName, $filepath)) {
                    $attachStmt->bindValue(':student_id', $student_id);
                    $attachStmt->bindValue(':type', $document_type);
                    $attachStmt->bindValue(':uri', $filename);
                    $attachStmt->bindValue(':uploaded_by', $_SESSION['user_id']);
                    if (!$attachStmt->execute()) {
                        throw new Exception("Failed to save file record");
                    }
                } else {
                    throw new Exception("Failed to move file");
                }
            } else {
                throw new Exception("File upload error: " . $_FILES['attachment_file']['error'][$index]);
            }
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Student registered successfully',
        'student_id' => $student_id
    ]);
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
