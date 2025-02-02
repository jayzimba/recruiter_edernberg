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

    // Add status filter to query if provided
    $statusFilter = "";
    $params = [];

    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $statusFilter = " AND s.application_status = :status";
        $params[':status'] = $_GET['status'];
    }

    $searchFilter = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchFilter = " AND (s.email LIKE :search 
                          OR s.G_ID LIKE :search 
                          OR s.contact LIKE :search)";
        $params[':search'] = "%" . $_GET['search'] . "%";
    }

    $query = "SELECT s.*, 
              p.program_name,
              ast.name as status_name,
              u.firstname as recruiter_firstname,
              u.lastname as recruiter_lastname
              FROM students s
              LEFT JOIN programs p ON s.program_id = p.id
              LEFT JOIN application_status ast ON s.application_status = ast.id
              LEFT JOIN users u ON s.recruiter_id = u.id
              WHERE 1=1" . $statusFilter . $searchFilter . "
              ORDER BY s.created_at DESC";

    $stmt = $conn->prepare($query);

    // Bind all parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
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
