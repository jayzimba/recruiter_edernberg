CREATE TABLE `student_grades` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `grade` VARCHAR(2) NOT NULL,
    `semester` INT NOT NULL,
    `academic_year` VARCHAR(9) NOT NULL,
    `grade_point` DECIMAL(3,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    CONSTRAINT `valid_grade` CHECK (
        `grade` IN ('A+', 'A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F')
    ),
    CONSTRAINT `valid_grade_point` CHECK (
        `grade_point` BETWEEN 0.00 AND 4.00
    ),
    CONSTRAINT `valid_semester` CHECK (
        `semester` IN (1, 2)
    ),
    UNIQUE KEY `unique_student_course` (`student_id`, `course_id`, `semester`, `academic_year`)
);

-- Insert sample data
INSERT INTO `student_grades` (`student_id`, `course_id`, `grade`, `semester`, `academic_year`, `grade_point`) VALUES
(1, 1, 'B+', 1, '2023/2024', 3.50),
(1, 2, 'A', 1, '2023/2024', 4.00),
(1, 3, 'A+', 1, '2023/2024', 4.00),
(1, 4, 'A+', 1, '2023/2024', 4.00),
(1, 5, 'A', 1, '2023/2024', 4.00),
(1, 6, 'A+', 1, '2023/2024', 4.00),
(1, 7, 'A+', 1, '2023/2024', 4.00); 