<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id, school_name 
              FROM schools 
              WHERE status = 1 
              ORDER BY school_name ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $schools = $stmt->fetchAll();
        
        echo json_encode([
            "status" => true,
            "message" => "Schools retrieved successfully",
            "data" => $schools
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "No schools found",
            "data" => []
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        "status" => false,
        "message" => "Database error: " . $e->getMessage(),
        "data" => null
    ]);
} 