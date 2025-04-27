<?php
header('Content-Type: application/json');
include_once 'config/database.php';
try {
    // Get and decode the input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // get the passed user id
    $userId = $input['user_id'];

    // Build the query
    $query = "
        SELECT 
            l.*,
            p.program_name as program_name,
            CONCAT(u.firstname, ' ', u.lastname) as recruiter_name
        FROM 
            leads l
            LEFT JOIN programs p ON l.program_id = p.id
            LEFT JOIN users u ON l.lead_recruiter_id = u.id
        WHERE 
            l.lead_recruiter_id = :recruiter_id
    ";

    $params = [':recruiter_id' => $userId];

    // Add filters if provided
    if (!empty($input['dateRange'])) {
        $dates = explode(' - ', $input['dateRange']);
        if (count($dates) == 2) {
            $query .= " AND DATE(l.created_at) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = date('Y-m-d', strtotime($dates[0]));
            $params[':end_date'] = date('Y-m-d', strtotime($dates[1]));
        }
    }

    if (!empty($input['program'])) {
        $query .= " AND l.program_id = :program_id";
        $params[':program_id'] = $input['program'];
    }

    if (isset($input['contactStatus']) && $input['contactStatus'] !== '') {
        $query .= " AND l.contacted = :contacted";
        $params[':contacted'] = $input['contactStatus'];
    }

    $query .= " ORDER BY l.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics
    $statsQuery = "
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN contacted = 1 THEN 1 ELSE 0 END) as contacted,
            SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as converted
        FROM leads 
        WHERE lead_recruiter_id = :recruiter_id
    ";
    
    $statsStmt = $conn->prepare($statsQuery);
    $statsStmt->execute([':recruiter_id' => $userId]);
    $statistics = $statsStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $leads,
        'statistics' => $statistics
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 