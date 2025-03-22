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

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Initialize filters
    $filters = [];
    $params = [];
    
    // Base query with proper table aliases and joins
    $query = "SELECT 
                l.*,
                p.program_name as program_name,
                CONCAT(u.firstname, ' ', u.lastname) as recruiter_name
              FROM leads l 
              LEFT JOIN programs p ON l.program_id = p.id 
              LEFT JOIN users u ON l.lead_recruiter_id = u.id 
              WHERE 1=1";
    
    // Add date range filter
    if (!empty($data['dateRange'])) {
        $dates = explode(' - ', $data['dateRange']);
        if (count($dates) == 2) {
            $filters[] = "l.created_at BETWEEN ? AND ?";
            $params[] = date('Y-m-d 00:00:00', strtotime($dates[0]));
            $params[] = date('Y-m-d 23:59:59', strtotime($dates[1]));
        }
    }
    
    // Add program filter
    if (!empty($data['program'])) {
        $filters[] = "l.program_id = ?";
        $params[] = $data['program'];
    }
    
    // Add contact status filter
    if (isset($data['contactStatus']) && $data['contactStatus'] !== '') {
        $filters[] = "l.contacted = ?";
        $params[] = $data['contactStatus'];
    }
    
    // Add country filter
    if (!empty($data['country'])) {
        $filters[] = "l.country = ?";
        $params[] = $data['country'];
    }

    // Add recruiter filter
    if (!empty($data['recruiter'])) {
        $filters[] = "l.lead_recruiter_id = ?";
        $params[] = $data['recruiter'];
    }
    
    // Combine filters
    if (!empty($filters)) {
        $query .= " AND " . implode(" AND ", $filters);
    }
    
    // Add ordering
    $query .= " ORDER BY l.created_at DESC";
    
    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    
    // Fetch results
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $statsQuery = "SELECT 
                    COUNT(*) as total,
                    COALESCE(SUM(CASE WHEN contacted = 1 THEN 1 ELSE 0 END), 0) as contacted,
                    COALESCE(SUM(CASE WHEN contacted = 0 THEN 1 ELSE 0 END), 0) as not_contacted,
                    COALESCE(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END), 0) as converted
                  FROM leads";
    
    $statsStmt = $conn->query($statsQuery);
    $statistics = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($statistics === false) {
        $statistics = [
            'total' => 0,
            'contacted' => 0,
            'not_contacted' => 0,
            'converted' => 0
        ];
    }

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