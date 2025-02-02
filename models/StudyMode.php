<?php
require_once __DIR__ . '/../config/database.php';

class StudyMode
{
    private $conn;
    private $table = "study_modes";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllModes()
    {
        try {
            $query = "SELECT id, mode_name FROM " . $this->table . " ORDER BY mode_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$result) {
                error_log("No study modes found in database");
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error fetching study modes: " . $e->getMessage());
            return false;
        }
    }

    public function getMode($id)
    {
        try {
            $query = "SELECT id, mode_name FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create($mode_name)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (mode_name) VALUES (:mode_name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":mode_name", $mode_name);

            if ($stmt->execute()) {
                return [
                    'status' => true,
                    'message' => 'Study mode created successfully',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to create study mode'
            ];
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
