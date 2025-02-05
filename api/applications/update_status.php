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
        // Generate student ID number
        $year = date('Y');

        // Get the last student number for this year
        $lastIdQuery = "SELECT MAX(CAST(SUBSTRING(student_id_number, 5) AS UNSIGNED)) as last_number 
                        FROM students 
                        WHERE student_id_number LIKE :year_prefix";

        $stmt = $conn->prepare($lastIdQuery);
        $yearPrefix = $year . '%';
        $stmt->bindParam(':year_prefix', $yearPrefix);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate next number
        $nextNumber = ($result['last_number'] ?? 0) + 1;
        $studentId = $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Update student ID number
        $updateIdQuery = "UPDATE students 
                         SET student_id_number = :student_id 
                         WHERE id = :id 
                         AND (student_id_number IS NULL OR student_id_number = '')";

        $stmt = $conn->prepare($updateIdQuery);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':id', $data['application_id']);
        $stmt->execute();

        // Get application details
        $query = "SELECT 
             s.firstname,
            s.lastname,
            s.middlename,
            s.email,
            s.contact,
            s.student_id_number,
            s.created_at,
            s.nationality,
            s.G_ID,
            s.student_id_number,
            i.intake_description as intake,
            p.program_name,
            p.duration,
            p.tuition_fee,
            l.description as level,
            sm.mode_name as study_mode,
            adm.admission_description as admission_type,
            CONCAT(u.firstname, ' ', u.lastname) as recruiter_name,
            u.email as recruiter_email,
            u.phone_number as recruiter_phone_number
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

        // Generate both DOCX and PDF versions
        $documentProcessor = new DocumentProcessor();
        $files = $documentProcessor->generateAdmissionLetter($applicationData);

        // Send email with both formats
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
                'G_ID' => $applicationData['G_ID'],
                'recruiter_email' => $applicationData['recruiter_email'],
                'student_id_number' => $applicationData['student_id_number'],
                'student_nationality' => $applicationData['nationality'],
                'tuition_fee' => $applicationData['tuition_fee'],
                'duration' => $applicationData['duration'],
                'admission_type' => $applicationData['admission_type'],
                'date_of_registration' => date('d/m/Y', strtotime('+1 month')),
                'date_of_commencement' => $applicationData['intake'],
                'recruiter_phone_number' => $applicationData['recruiter_phone_number']
            ],
            $files['pdf'] // Send the PDF version
        );

        // Store file paths in database
        $updateFilesQuery = "UPDATE students SET 
            admission_letter_docx = :docx,
            admission_letter_pdf = :pdf 
            WHERE id = :id";
        
        $stmt = $conn->prepare($updateFilesQuery);
        $stmt->bindParam(':docx', $files['docx']);
        $stmt->bindParam(':pdf', $files['pdf']);
        $stmt->bindParam(':id', $data['application_id']);
        $stmt->execute();
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
