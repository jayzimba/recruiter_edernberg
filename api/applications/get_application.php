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

$database = new Database();
$conn = $database->getConnection();

try {
    $application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $recruiter_id = $_SESSION['user_id'];

    if (!$application_id) {
        throw new Exception('Invalid application ID');
    }

    // Get application details
    $query = "SELECT s.*, 
              p.program_name, sm.mode_name, 
              i.intake_description, 
              at.admission_description,
              status.name as status_name,
              CAST(status.id AS UNSIGNED) as status_id
              FROM students s
              LEFT JOIN programs p ON s.program_id = p.id
              LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
              LEFT JOIN intake i ON s.intake_id = i.id
              LEFT JOIN admission_type at ON s.admission_type = at.id
              LEFT JOIN application_status status ON s.application_status = status.id
              WHERE s.id = :id AND s.recruiter_id = :recruiter_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $application_id);
    $stmt->bindParam(':recruiter_id', $recruiter_id);
    $stmt->execute();

    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        throw new Exception('Application not found');
    }

    // Get attachments
    $attachQuery = "SELECT a.*, dt.description 
                   FROM attachments a
                   LEFT JOIN attachment_type dt ON a.type = dt.id
                   WHERE a.student = :student_id";
    $attachStmt = $conn->prepare($attachQuery);
    $attachStmt->bindParam(':student_id', $application_id);
    $attachStmt->execute();

    $application['attachments'] = $attachStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $application
    ]);
} catch (Exception $e) {
    error_log("Error getting application details: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
