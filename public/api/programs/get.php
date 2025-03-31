<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Validate school_id parameter
    if(!isset($_GET['school_id']) || empty($_GET['school_id'])) {
        throw new Exception("School ID is required");
    }

    $school_id = intval($_GET['school_id']);

    $query = "SELECT p.id, p.program_name, sm.name as mode_name
              FROM programs p
              LEFT JOIN study_modes sm ON p.study_mode_id = sm.id
              WHERE p.school_id = :school_id 
              AND p.status = 1
              ORDER BY p.program_name ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":school_id", $school_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $programs = $stmt->fetchAll();
        
        echo json_encode([
            "status" => true,
            "message" => "Programs retrieved successfully",
            "data" => $programs
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "No programs found for this school",
            "data" => []
        ]);
    }
} catch(Exception $e) {
    echo json_encode([
        "status" => false,
        "message" => $e->getMessage(),
        "data" => null
    ]);
} 