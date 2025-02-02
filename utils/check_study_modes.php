<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    // Check if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'study_modes'");
    $tableExists = $stmt->rowCount() > 0;
    echo "Study modes table exists: " . ($tableExists ? "Yes" : "No") . "\n";

    if ($tableExists) {
        // Count records
        $stmt = $conn->query("SELECT COUNT(*) as count FROM study_modes");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Number of study modes: " . $count['count'] . "\n";

        // List all modes
        $stmt = $conn->query("SELECT * FROM study_modes ORDER BY mode_name");
        $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nAvailable modes:\n";
        foreach ($modes as $mode) {
            echo "- {$mode['mode_name']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
