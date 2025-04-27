<?php
require_once __DIR__ . '/BaseModel.php';

class Student extends BaseModel
{
    protected $table_name = 'students';

    public function __construct()
    {
        parent::__construct();
    }

    public function getDashboardData($student_id)
    {
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

    public function getAcademicInfo($student_id)
    {
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

    private function getRegisteredProgramsCount($student_id)
    {
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

    private function getRegisteredCoursesCount($student_id)
    {
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

    private function getUpcomingExamsCount($student_id)
    {
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

    private function getFeeBalance($student_id)
    {
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

    public function getCurrentSemesterCourses($student_id)
    {
        try {
            // Get student's academic year and program
            $academicInfo = $this->getAcademicInfo($student_id);
            if (!$academicInfo) {
                return [];
            }

            $sql = "SELECT 
                    c.course_code,
                    c.course_name,
                    c.description,
                    c.credits,
                    CONCAT(i.first_name, ' ', i.last_name) as instructor_name,
                    sc.status as enrollment_status,
                    sc.registration_date
                FROM courses c
                LEFT JOIN student_courses sc ON c.course_code = sc.course_code 
                    AND sc.student_id = :student_id
                LEFT JOIN instructors i ON c.instructor_id = i.id
                WHERE c.semester = :current_semester
                AND c.status = 'active'
                ORDER BY c.course_code";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->bindParam(":current_semester", $academicInfo['current_semester']);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCurrentSemesterCourses: " . $e->getMessage());
            return [];
        }
    }

    public function getProfile($student_id)
    {
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

    public function getCourses($student_id)
    {
        $sql = "SELECT c.*, e.status as enrollment_status, e.grade 
                FROM courses c 
                JOIN enrollments e ON c.id = e.course_id 
                WHERE e.student_id = :student_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFinancialStatus($student_id)
    {
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

    public function updateProfile($student_id, $data)
    {
        return $this->update($student_id, $data);
    }

    public function authenticate($student_id, $password)
    {
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

    public function getAcademicCalendar($student_id)
    {
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

    public function getCourseDetails($course_code)
    {
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

    public function getCourseModules($course_code)
    {
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

    public function getProfileData($student_id)
    {
        try {
            $query = "SELECT s.*, 
                      s.phone_number as phone,
                      s.date_of_birth,
                      s.nationality,
                      s.address,
                      p.program_name, 
                      COALESCE(d.department_name, 'Department Not Assigned') as department_name, 
                      sp.enrollment_date, 
                      sp.current_year, 
                      sp.current_semester, 
                      sp.status as program_status, 
                      ay.year_name as academic_year 
                      FROM students s 
                      LEFT JOIN student_programs sp ON s.student_id = sp.student_id AND sp.status = 'active' 
                      LEFT JOIN programs p ON sp.program_id = p.program_id 
                      LEFT JOIN departments d ON p.department_id = d.id 
                      LEFT JOIN academic_years ay ON sp.academic_year_id = ay.id 
                      WHERE s.student_id = :student_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();

            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($profile) {
                // Format the date of birth
                if (!empty($profile['date_of_birth'])) {
                    $profile['date_of_birth'] = date('M d, Y', strtotime($profile['date_of_birth']));
                }

                // Format enrollment date
                if (!empty($profile['enrollment_date'])) {
                    $profile['enrollment_date'] = date('M d, Y', strtotime($profile['enrollment_date']));
                }

                // Format registration date
                if (!empty($profile['registration_date'])) {
                    $profile['registration_date'] = date('M d, Y', strtotime($profile['registration_date']));
                }

                // Set default values for missing information
                $profile['program_name'] = $profile['program_name'] ?? 'Program Not Assigned';
                $profile['department_name'] = $profile['department_name'] ?? 'Department Not Assigned';
                $profile['email'] = $profile['email'] ?? 'Not Set';
                $profile['phone'] = $profile['phone'] ?? 'Not Set';
                $profile['address'] = $profile['address'] ?? 'Not Set';
                $profile['gender'] = $profile['gender'] ?? 'Not Set';
                $profile['nationality'] = $profile['nationality'] ?? 'Not Set';
                $profile['academic_year'] = $profile['academic_year'] ?? 'Not Set';
                $profile['current_year'] = $profile['current_year'] ?? 'Not Set';
                $profile['current_semester'] = $profile['current_semester'] ?? 'Not Set';
                $profile['program_status'] = ucfirst(strtolower($profile['program_status'] ?? 'Not Enrolled'));

                return $profile;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Error fetching profile data: " . $e->getMessage());
            return null;
        }
    }

    public function isRegisteredForCourse($studentId, $courseCode) {
        $sql = "SELECT COUNT(*) as count FROM student_courses 
                WHERE student_id = ? AND course_code = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$studentId, $courseCode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    public function registerCourse($studentId, $courseCode) {
        // First, get student's current academic info
        $academicInfo = $this->getAcademicInfo($studentId);
        
        if (!$academicInfo) {
            return false;
        }

        $sql = "INSERT INTO student_courses (student_id, course_code, academic_year_id, 
                semester, registration_date, status) 
                VALUES (?, ?, ?, ?, NOW(), 'active')";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $studentId, 
            $courseCode, 
            $academicInfo['academic_year_id'], 
            $academicInfo['current_semester']
        ]);
    }

    public function dropCourse($studentId, $courseCode) {
        $sql = "UPDATE student_courses SET status = 'dropped', 
                dropped_date = NOW() 
                WHERE student_id = ? AND course_code = ? 
                AND status = 'active'";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$studentId, $courseCode]);
    }
}
