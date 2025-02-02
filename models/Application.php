<?php
require_once __DIR__ . '/../config/database.php';

class Application
{
    private $conn;
    private $table = "students";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($data)
    {
        try {
            $query = "INSERT INTO " . $this->table . "
                    (firstname, lastname, middlename, email, contact, 
                     program_id, study_mode_id, recruiter_id, application_status)
                    VALUES
                    (:firstname, :lastname, :middlename, :email, :contact,
                     :program_id, :study_mode_id, :recruiter_id, 
                     (SELECT id FROM application_status WHERE name = 'Pending'))";

            $stmt = $this->conn->prepare($query);

            // Bind values
            $stmt->bindParam(":firstname", $data->firstname);
            $stmt->bindParam(":lastname", $data->lastname);
            $stmt->bindParam(":middlename", $data->middlename);
            $stmt->bindParam(":email", $data->email);
            $stmt->bindParam(":contact", $data->contact);
            $stmt->bindParam(":program_id", $data->program_id);
            $stmt->bindParam(":study_mode_id", $data->study_mode_id);
            $stmt->bindParam(":recruiter_id", $data->recruiter_id);

            if ($stmt->execute()) {
                return [
                    'status' => true,
                    'message' => 'Application submitted successfully'
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to submit application'
            ];
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateApplicationStatus($application_id, $status, $commencement_date = null)
    {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET application_status = (SELECT id FROM application_status WHERE name = :status)";

            if ($commencement_date) {
                $query .= ", commencement_date = :commencement_date";
            }

            $query .= " WHERE id = :application_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":application_id", $application_id);

            if ($commencement_date) {
                $stmt->bindParam(":commencement_date", $commencement_date);
            }

            if ($stmt->execute()) {
                return [
                    'status' => true,
                    'message' => 'Application status updated successfully'
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to update application status'
            ];
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getApplicationDetails($application_id)
    {
        try {
            $query = "SELECT s.*, 
                             p.program_name, 
                             p.tuition_fee,
                             p.duration_months,
                             sc.school_name,
                             sm.mode_name as study_mode,
                             ast.name as status_name,
                             CONCAT(u.firstname, ' ', u.lastname) as recruiter_name
                     FROM " . $this->table . " s
                     LEFT JOIN programs p ON s.program_id = p.id
                     LEFT JOIN schools sc ON p.school_id = sc.id
                     LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
                     LEFT JOIN application_status ast ON s.application_status = ast.id
                     LEFT JOIN users u ON s.recruiter_id = u.id
                     WHERE s.id = :application_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":application_id", $application_id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
