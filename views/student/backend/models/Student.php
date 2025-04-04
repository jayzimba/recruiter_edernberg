<?php
require_once __DIR__ . '/BaseModel.php';

class Student extends BaseModel {
    protected $table_name = 'students';

    public function __construct() {
        parent::__construct();
    }

    public function getDashboardData($student_id) {
        try {
            // Get academic info
            $academic_info = $this->getAcademicInfo($student_id);
            
            // Get registered programs count
            $programs_count = $this->getRegisteredProgramsCount($student_id);
            
            // Get registered courses count
            $courses_count = $this->getRegisteredCoursesCount($student_id);
            
            // Get upcoming exams count
            $upcoming_exams = $this->getUpcomingExamsCount($student_id);
            
            // Get fee balance
            $fee_balance = $this->getFeeBalance($student_id);

            return [
                'academic_info' => $academic_info,
                'programs_count' => $programs_count,
                'courses_count' => $courses_count,
                'upcoming_exams' => $upcoming_exams,
                'fee_balance' => $fee_balance
            ];
        } catch (PDOException $e) {
            error_log("Error fetching dashboard data: " . $e->getMessage());
            return false;
        }
    }

    private function getAcademicInfo($student_id) {
        $sql = "SELECT 
                    ay.id as academic_year_id,
                    ay.year_name as academic_year,
                    l.level_name as year_of_study,
                    CASE 
                        WHEN MONTH(CURRENT_DATE) BETWEEN 1 AND 6 THEN '1st Semester'
                        ELSE '2nd Semester'
                    END as current_semester
                FROM students s
                LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                LEFT JOIN levels l ON s.level_id = l.id
                WHERE s.student_id = :student_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getRegisteredProgramsCount($student_id) {
        $sql = "SELECT COUNT(DISTINCT sp.program_id) as count
                FROM student_programs sp
                WHERE sp.student_id = :student_id
                AND sp.status = 'active'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    private function getRegisteredCoursesCount($student_id) {
        $sql = "SELECT COUNT(DISTINCT sc.course_offering_id) as count
                FROM student_courses sc
                WHERE sc.student_id = :student_id
                AND sc.status = 'enrolled'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    private function getUpcomingExamsCount($student_id) {
        $sql = "SELECT COUNT(DISTINCT e.id) as count
                FROM examinations e
                JOIN student_courses sc ON e.course_offering_id = sc.course_offering_id
                WHERE sc.student_id = :student_id
                AND e.exam_date >= CURRENT_DATE
                AND e.status = 'scheduled'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    private function getFeeBalance($student_id) {
        $sql = "SELECT COALESCE(SUM(balance), 0) as balance
                FROM student_fees
                WHERE student_id = :student_id
                AND status IN ('unpaid', 'partial')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['balance'] ?? 0;
    }

    public function getCurrentSemesterCourses($student_id) {
        try {
            // Get student's academic year and program
            $academicInfo = $this->getAcademicInfo($student_id);
            if (!$academicInfo) {
                return [];
            }

            $sql = "SELECT 
                        c.course_code,
                        c.course_name,
                        c.credits,
                        co.id as course_offering_id,
                        co.start_date,
                        co.end_date,
                        CONCAT(i.first_name, ' ', i.last_name) as instructor_name,
                        sc.progress_percentage,
                        CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END as is_enrolled
                    FROM courses c
                    JOIN course_offerings co ON c.course_code = co.course_code
                    JOIN instructors i ON co.instructor_id = i.instructor_id
                    LEFT JOIN student_courses sc ON co.id = sc.course_offering_id AND sc.student_id = :student_id
                    WHERE c.year_of_study = (
                        SELECT l.level_name 
                        FROM students s 
                        JOIN levels l ON s.level_id = l.id 
                        WHERE s.student_id = :student_id
                    )
                    AND c.semester = (
                        CASE 
                            WHEN MONTH(CURRENT_DATE) BETWEEN 1 AND 6 THEN '1st Semester'
                            ELSE '2nd Semester'
                        END
                    )
                    AND co.academic_year_id = :academic_year_id
                    AND co.status = 'ongoing'
                    AND c.status = 'active'
                    ORDER BY c.course_code";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->bindParam(":academic_year_id", $academicInfo['academic_year_id']);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getCurrentSemesterCourses: " . $e->getMessage());
            return [];
        }
    }

    public function getProfile($student_id) {
        $sql = "SELECT s.*, p.program_name, l.level_name, l.level_description 
                FROM students s 
                LEFT JOIN programs p ON s.program_id = p.id 
                LEFT JOIN levels l ON s.level_id = l.id 
                WHERE s.student_id = :student_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getCourses($student_id) {
        $sql = "SELECT c.*, e.status as enrollment_status, e.grade 
                FROM courses c 
                JOIN enrollments e ON c.id = e.course_id 
                WHERE e.student_id = :student_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFinancialStatus($student_id) {
        $sql = "SELECT f.*, p.amount as payment_amount, p.payment_date, p.status as payment_status 
                FROM fees f 
                LEFT JOIN payments p ON f.id = p.fee_id 
                WHERE f.student_id = :student_id 
                ORDER BY f.due_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateProfile($student_id, $data) {
        return $this->update($student_id, $data);
    }

    public function authenticate($student_id, $password) {
        try {
            $sql = "SELECT s.*, u.password, u.email, u.status as user_status 
                    FROM students s 
                    JOIN users u ON s.user_id = u.id 
                    WHERE s.student_id = :student_id 
                    AND u.role = 'student' 
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->execute();
            
            $student = $stmt->fetch();
            
            if ($student && password_verify($password, $student['password']) && $student['user_status'] === 'active') {
                // Remove sensitive data
                unset($student['password']);
                unset($student['user_status']);
                return $student;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }

    public function getAcademicCalendar($student_id) {
        try {
            // Get student's academic info first
            $academicInfo = $this->getAcademicInfo($student_id);
            if (!$academicInfo || !isset($academicInfo['academic_year_id'])) {
                return [];
            }

            $sql = "SELECT ac.*, ay.year_name as academic_year 
                    FROM academic_calendar ac
                    JOIN academic_years ay ON ac.academic_year_id = ay.id
                    WHERE ac.academic_year_id = :academic_year_id
                    AND ac.event_date >= CURDATE()
                    ORDER BY ac.event_date ASC
                    LIMIT 2";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':academic_year_id', $academicInfo['academic_year_id']);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAcademicCalendar: " . $e->getMessage());
            return [];
        }
    }

    public function getCourseDetails($course_code) {
        try {
            $sql = "SELECT c.description, co.start_date, co.end_date
                    FROM courses c
                    JOIN course_offerings co ON c.course_code = co.course_code
                    WHERE c.course_code = ?";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$course_code]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCourseDetails: " . $e->getMessage());
            return null;
        }
    }

    public function getCourseModules($course_code) {
        try {
            $query = "SELECT module_number, module_title, description 
                      FROM course_modules 
                      WHERE course_code = :course_code 
                      ORDER BY module_number";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':course_code', $course_code);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching course modules: " . $e->getMessage());
            return array();
        }
    }
}
?> 