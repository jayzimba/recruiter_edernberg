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
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $recruiter_id = $_SESSION['user_id'];

    $query = "SELECT s.*, p.program_name, status.name as status_name
              FROM students s
              LEFT JOIN programs p ON s.program_id = p.id
              LEFT JOIN application_status status ON s.application_status = status.id
              WHERE s.recruiter_id = :recruiter_id";

    if ($search) {
        $query .= " AND (s.email LIKE :search OR s.G_ID LIKE :search)";
    }

    $query .= " ORDER BY s.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':recruiter_id', $recruiter_id);

    if ($search) {
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
    }

    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $applications
    ]);
} catch (Exception $e) {
    error_log("Error fetching applications: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch applications'
    ]);
}
