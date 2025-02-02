<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$modes = [
    'Full Time',
    'Part Time',
    'Distance Learning',
    'Block Release',
    'Evening Classes',
    'Weekend Classes',
    'Online Learning'
];

try {
    // First check if modes exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM study_modes");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($count['count'] == 0) {
        echo "Populating study modes...\n";

        $stmt = $conn->prepare("INSERT INTO study_modes (mode_name) VALUES (:mode_name)");

        foreach ($modes as $mode) {
            $stmt->bindParam(':mode_name', $mode);
            $stmt->execute();
            echo "Added mode: $mode\n";
        }

        echo "Study modes populated successfully!\n";
    } else {
        echo "Study modes already exist in the database.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
