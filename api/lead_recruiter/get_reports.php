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
    $data = json_decode(file_get_contents('php://input'), true);
    $database = new Database();
    $conn = $database->getConnection();

    // Initialize parameters array
    $params = [];
    $whereConditions = [];

    // Base query for applications
    $query = "
        SELECT 
            s.id,
            s.student_id_number,
            s.firstname,
            s.middlename,
            s.lastname,
            CONCAT(s.firstname, ' ', COALESCE(s.middlename, ''), ' ', s.lastname) as student_name,
            s.email,
            s.contact,
            s.nationality,
            s.G_ID,
            p.program_name,
            CONCAT(u.firstname, ' ', u.lastname) as recruiter_name,
            s.created_at as application_date,
            s.application_status,
            s.commencement_date,
            s.updated_at,
            sm.mode_name as study_mode,
            l.description as program_level
        FROM students s
        LEFT JOIN programs p ON s.program_id = p.id
        LEFT JOIN users u ON s.recruiter_id = u.id
        LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
        LEFT JOIN levels l ON p.level_id = l.id
        WHERE 1=1
    ";

    // Add date range filter
    if (!empty($data['dateRange'])) {
        $dates = explode(' - ', $data['dateRange']);
        $startDate = date('Y-m-d', strtotime($dates[0]));
        $endDate = date('Y-m-d', strtotime($dates[1]));
        $whereConditions[] = "DATE(s.created_at) BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $startDate;
        $params[':end_date'] = $endDate;
    }

    // Add recruiter filter
    if (!empty($data['recruiter'])) {
        $whereConditions[] = "s.recruiter_id = :recruiter_id";
        $params[':recruiter_id'] = $data['recruiter'];
    }

    // Add program filter
    if (!empty($data['program'])) {
        $whereConditions[] = "s.program_id = :program_id";
        $params[':program_id'] = $data['program'];
    }

    // Add status filter
    if (!empty($data['status'])) {
        $whereConditions[] = "s.application_status = :status";
        $params[':status'] = $data['status'];
    }

    // Combine where conditions
    if (!empty($whereConditions)) {
        $query .= " AND " . implode(" AND ", $whereConditions);
    }

    // Add ordering
    $query .= " ORDER BY s.created_at DESC";

    // Execute main query
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics
    $statistics = [
        'total' => 0,
        '1' => 0, // Pending
        '2' => 0, // Under Review
        '3' => 0, // Approved
        '4' => 0, // Rejected
        'programs' => []
    ];

    // Build statistics query with the same filters
    $statsQuery = "
        SELECT 
            application_status,
            COUNT(*) as count
        FROM students s
        WHERE 1=1
    ";
    if (!empty($whereConditions)) {
        $statsQuery .= " AND " . implode(" AND ", $whereConditions);
    }
    $statsQuery .= " GROUP BY application_status";

    $stmt = $conn->prepare($statsQuery);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($statusCounts as $count) {
        $statistics[$count['application_status']] = (int)$count['count'];
        $statistics['total'] += (int)$count['count'];
    }

    // Get program statistics
    $programQuery = "
        SELECT 
            p.program_name as name,
            COUNT(*) as count
        FROM students s
        JOIN programs p ON s.program_id = p.id
        WHERE 1=1
    ";
    if (!empty($whereConditions)) {
        $programQuery .= " AND " . implode(" AND ", $whereConditions);
    }
    $programQuery .= " GROUP BY p.id, p.program_name ORDER BY count DESC";

    $stmt = $conn->prepare($programQuery);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $statistics['programs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response based on whether this is an export request
    if (!empty($data['export'])) {
        // For Excel export, return full details
        $formattedResults = array_map(function($row) {
            return [
                'student_id_number' => $row['student_id_number'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'middlename' => $row['middlename'],
                'email' => $row['email'],
                'contact' => $row['contact'],
                'nationality' => $row['nationality'],
                'G_ID' => $row['G_ID'],
                'program_name' => $row['program_name'],
                'study_mode' => $row['study_mode'],
                'program_level' => $row['program_level'],
                'recruiter_name' => $row['recruiter_name'],
                'created_at' => $row['application_date'],
                'application_status' => $row['application_status'],
                'commencement_date' => $row['commencement_date'],
                'updated_at' => $row['updated_at']
            ];
        }, $results);
    } else {
        // For table display, return simplified data
        $formattedResults = array_map(function($row) {
            return [
                'id' => $row['id'],
                'student_name' => $row['student_name'],
                'program_name' => $row['program_name'],
                'recruiter_name' => $row['recruiter_name'],
                'application_date' => $row['application_date'],
                'status' => $row['application_status']
            ];
        }, $results);
    }

    echo json_encode([
        'status' => true,
        'data' => $formattedResults,
        'statistics' => $statistics
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 