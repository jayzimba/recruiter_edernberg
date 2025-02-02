<?php
// Prevent any output before JSON response
ob_clean();

// Disable error display but log them
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Ensure JSON response
header('Content-Type: application/json');

try {
    require_once '../../middleware/Auth.php';
    require_once '../../config/database.php';

    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        throw new Exception('Unauthorized access');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Debug file upload
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("POST data: " . print_r($_POST, true));

    if (!isset($_FILES['pop_file']) || !isset($_POST['application_id'])) {
        throw new Exception('Missing required fields');
    }

    $application_id = intval($_POST['application_id']);
    session_start();
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Session expired. Please log in again.');
    }
    $recruiter_id = $_SESSION['user_id'];

    // Verify application belongs to recruiter
    $checkQuery = "SELECT id FROM students WHERE id = :id AND recruiter_id = :recruiter_id";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':id', $application_id, PDO::PARAM_INT);
    $stmt->bindParam(':recruiter_id', $recruiter_id, PDO::PARAM_INT);
    $stmt->execute();

    if (!$stmt->fetch()) {
        throw new Exception('Application not found or unauthorized access.');
    }

    // Handle file upload
    $file = $_FILES['pop_file'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        ];
        throw new Exception('Upload error: ' . ($uploadErrors[$file['error']] ?? 'Unknown error'));
    }

    $fileName = $file['name'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file type
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only PDF, JPG, and PNG files are allowed.');
    }

    // Generate unique filename
    $newFileName = 'pop_' . $application_id . '_' . time() . '.' . $fileType;

    // Define upload directories
    $baseUploadDir = realpath(__DIR__ . '/../../uploads');
    $uploadDir = $baseUploadDir ? $baseUploadDir . '/proof_of_payment' : null;

    if (!$uploadDir) {
        throw new Exception("Base upload directory does not exist.");
    }

    // Ensure directories exist and are writable
    foreach ([$baseUploadDir, $uploadDir] as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new Exception("Cannot create directory: $dir");
            }
        }
        if (!is_writable($dir)) {
            throw new Exception("Directory not writable: $dir. Check permissions.");
        }
    }

    $uploadPath = $uploadDir . '/' . $newFileName;
    error_log("Attempting to upload file to: " . $uploadPath);

    // Start transaction
    $conn->beginTransaction();

    // Move the uploaded file
    if (!@move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to upload file. Check permissions and retry.');
    }

    // Update student record
    $updateQuery = "UPDATE students 
                    SET pop = :pop_file, 
                        application_status = 2
                    WHERE id = :id";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':pop_file', $newFileName, PDO::PARAM_STR);
    $stmt->bindParam(':id', $application_id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        @unlink($uploadPath);
        throw new Exception('Failed to update database record.');
    }

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Proof of payment uploaded successfully'
    ]);
} catch (Throwable $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Upload error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
