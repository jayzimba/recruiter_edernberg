<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;


class DocumentProcessor
{
    private $uploadsDir;

    public function __construct()
    {
        // Use DIRECTORY_SEPARATOR for cross-platform compatibility
        $this->uploadsDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . 
                           DIRECTORY_SEPARATOR . 'admissions' . DIRECTORY_SEPARATOR;

        // Create directories with error handling
        $this->ensureDirectoryExists($this->uploadsDir);

        // Set custom temp directory for PhpWord
        $tempDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . 
                  DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        $this->ensureDirectoryExists($tempDir);
        
        \PhpOffice\PhpWord\Settings::setTempDir($tempDir);
    }

    private function ensureDirectoryExists($dir)
    {
        if (!file_exists($dir)) {
            try {
                if (!mkdir($dir, 0777, true)) {
                    throw new Exception("Failed to create directory: $dir");
                }
                // On Windows, mkdir permission parameter is ignored
                // Explicitly set permissions after creation
                chmod($dir, 0777);
            } catch (Exception $e) {
                error_log("Directory creation error: " . $e->getMessage());
                error_log("Path: $dir");
                error_log("Current user: " . get_current_user());
                error_log("PHP process user: " . shell_exec('whoami'));
                throw new Exception("Failed to create or set permissions on directory: $dir");
            }
        }

        // Verify directory is writable
        if (!is_writable($dir)) {
            error_log("Directory not writable: $dir");
            error_log("Current permissions: " . substr(sprintf('%o', fileperms($dir)), -4));
            throw new Exception("Directory not writable: $dir");
        }
    }

    public function generateAdmissionLetter($applicationData)
    {
        try {
            // Debug log
            error_log("Starting document generation with data: " . json_encode($applicationData));

            // Select template based on study mode
            $studyMode = strtolower($applicationData['study_mode'] ?? '');
            $isDistance = in_array($studyMode, ['distance learning', 'online learning'], true);
            $templatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . 
                          DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
                          ($isDistance ? 'distance_admission.docx' : 'fulltime_admission.docx');

            error_log("Using template: " . $templatePath);

            if (!file_exists($templatePath)) {
                throw new Exception('Template file not found: ' . $templatePath);
            }

            // Load template
            try {
                $templateProcessor = new TemplateProcessor($templatePath);
            } catch (Exception $e) {
                error_log("Template processing error: " . $e->getMessage());
                throw $e;
            }

            // Replace variables in template
            $templateProcessor->setValue('student_name', ($applicationData['firstname'] ?? '') . ' ' . ($applicationData['lastname'] ?? ''));
            $templateProcessor->setValue('student_contact', $applicationData['contact'] ?? '');
            $templateProcessor->setValue('student_email', $applicationData['email'] ?? '');
            $templateProcessor->setValue('student_id_number', $applicationData['student_id_number'] ?? '');
            $templateProcessor->setValue('recruiter_name', $applicationData['recruiter_name'] ?? '');
            $templateProcessor->setValue('student_program', $applicationData['program_name'] ?? '');
            $templateProcessor->setValue('student_admission_type', $applicationData['admission_type'] ?? '');
            $templateProcessor->setValue('date', date('d/m/Y'));
            $templateProcessor->setValue('date_of_registration', date('d/m/Y'));
            $templateProcessor->setValue('date_of_commencement', date('d/m/Y', strtotime('+1 week')));

            // Create a safe filename from student name and date
            $safeName = preg_replace(
                '/[^a-zA-Z0-9]/',
                '_',
                strtolower($applicationData['firstname'] . '_' . $applicationData['lastname'])
            );
            
            // Use DIRECTORY_SEPARATOR for file paths
            $fileName = $safeName . '_' . date('Y_m_d') . '.docx';
            $outputFile = $this->uploadsDir . $fileName;

            error_log("Saving to: " . $outputFile);
            $templateProcessor->saveAs($outputFile);

            return $outputFile;
        } catch (Exception $e) {
            error_log("Document generation error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }
}
