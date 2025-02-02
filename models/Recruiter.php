<?php
require_once __DIR__ . '/../config/database.php';

class Recruiter
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createRecruiter($data)
    {
        try {
            // First check if email or NRC already exists
            $checkQuery = "SELECT id FROM " . $this->table . " 
                         WHERE email = :email OR nrc_number = :nrc_number";

            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(":email", $data->email);
            $stmt->bindParam(":nrc_number", $data->nrc_number);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return [
                    'status' => false,
                    'message' => 'Email or NRC number already exists'
                ];
            }

            // Default password
            $defaultPassword = "Password@2025";
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

            $query = "INSERT INTO " . $this->table . "
                    (firstname, lastname, nrc_number, email, phone_number, password, role_id)
                    VALUES
                    (:firstname, :lastname, :nrc_number, :email, :phone_number, :password,
                     (SELECT id FROM user_roles WHERE name = :role))";

            $stmt = $this->conn->prepare($query);

            // Bind values
            $stmt->bindParam(":firstname", $data->firstname);
            $stmt->bindParam(":lastname", $data->lastname);
            $stmt->bindParam(":nrc_number", $data->nrc_number);
            $stmt->bindParam(":email", $data->email);
            $stmt->bindParam(":phone_number", $data->phone_number);
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":role", $data->role); // 'recruiter' or 'lead_recruiter'

            if ($stmt->execute()) {
                // You might want to send an email to the recruiter with their credentials
                return [
                    'status' => true,
                    'message' => 'Recruiter created successfully',
                    'default_password' => $defaultPassword
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to create recruiter'
            ];
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAllRecruiters()
    {
        try {
            $query = "SELECT u.*, r.name as role_name 
                     FROM " . $this->table . " u
                     LEFT JOIN user_roles r ON u.role_id = r.id
                     WHERE r.name IN ('recruiter', 'lead_recruiter')
                     ORDER BY u.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
