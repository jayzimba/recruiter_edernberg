<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;


class DocumentProcessor
{
    private $uploadsDir;

    public function __construct()
    {
        // Set uploads directory
        $this->uploadsDir = dirname(__DIR__) . '/uploads/admissions/';
        if (!file_exists($this->uploadsDir)) {
            mkdir($this->uploadsDir, 0777, true);
        }

        // Set custom temp directory for PhpWord
        $tempDir = dirname(__DIR__) . '/uploads/temp/';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        \PhpOffice\PhpWord\Settings::setTempDir($tempDir);
    }

    public function generateAdmissionLetter($applicationData)
    {
        try {
            // Debug log
            error_log("Starting document generation with data: " . json_encode($applicationData));

            // Select template based on study mode
            $studyMode = strtolower($applicationData['study_mode'] ?? '');
            $isDistance = in_array($studyMode, ['distance learning', 'online learning'], true);
            $templatePath = dirname(__DIR__) . '/assets/templates/' .
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
            $docxFile = $this->uploadsDir . $safeName . '_' . date('Y_m_d') . '.docx';
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
