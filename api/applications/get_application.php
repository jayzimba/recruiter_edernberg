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
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $recruiter_id = $_SESSION['user_id'];

    $query = "SELECT s.*, p.program_name, status.name as status_name
              FROM students s
              LEFT JOIN programs p ON s.program_id = p.id
              LEFT JOIN application_status AS status ON s.application_status = status.id
              WHERE s.recruiter_id = :recruiter_id";

    if (!empty($id)) {
        $query .= " AND s.id = :id";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':recruiter_id', $recruiter_id, PDO::PARAM_INT);

    if (!empty($id)) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    $stmt->execute();

    $application = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single application

    if ($application) {
        echo json_encode([
            'status' => true,
            'data' => $application
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Application not found'
        ]);
    }
} catch (Exception $e) {
    error_log("Error fetching application: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch application'
    ]);
}