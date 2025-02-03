<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../config/database.php';
require_once '../../utils/DocumentProcessor.php';
require_once '../../utils/Mailer.php';

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

    if (!isset($data['application_id']) || !isset($data['status'])) {
        throw new Exception('Missing required fields');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // If status is "Accept" (status_id = 3), generate and send admission letter
    if ($data['status'] == 3) {
        // Get application details
        $query = "SELECT 
            s.firstname,
            s.lastname,
            s.middlename,
            s.email,
            s.contact,
            s.G_ID,
            s.created_at,
            i.intake_description as intake,
            p.program_name,
            p.duration,
            l.description as level,
            sm.mode_name as study_mode,
            adm.admission_description as admission_type,
            CONCAT(u.firstname, ' ', u.lastname) as recruiter_name,
            u.email as recruiter_email
        FROM students s
        LEFT JOIN programs p ON s.program_id = p.id
        LEFT JOIN intake i ON s.intake_id = i.id
        LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
        LEFT JOIN admission_type adm ON s.admission_type = adm.id
        LEFT JOIN users u ON s.recruiter_id = u.id
        LEFT JOIN levels l ON p.level_id = l.id

        WHERE s.id = :id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $data['application_id']);
        $stmt->execute();
        $applicationData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate admission letter
        $docProcessor = new DocumentProcessor();
        $docxPath = $docProcessor->generateAdmissionLetter($applicationData);

        // Send email with attachment
        $mailer = new Mailer();
        $mailer->sendAdmissionLetter(
            $applicationData['email'],
            [
                'student_name' => $applicationData['firstname'] . ' ' .
                    ($applicationData['middlename'] ? ' ' . $applicationData['middlename'] . ' ' : ' ') .
                    $applicationData['lastname'],
                'program_name' => $applicationData['program_name'],
                'level' => $applicationData['level'],
                'study_mode' => $applicationData['study_mode'],
                'intake' => $applicationData['intake'],
                'recruiter_email' => $applicationData['recruiter_email']
            ],
            $docxPath
        );

        // Clean up DOCX file
        unlink($docxPath);
    }

    // Update application status
    $updateQuery = "UPDATE students 
                   SET application_status = :status,
                       updated_at = NOW()
                   WHERE id = :id";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':status', $data['status']);
    $stmt->bindParam(':id', $data['application_id']);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update application status');
    }

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Application status updated successfully'
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Error updating application status: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}