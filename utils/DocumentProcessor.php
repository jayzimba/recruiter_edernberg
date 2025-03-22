<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class DocumentProcessor
{
    private $uploadsDir;
    private $pdfDir;

    public function __construct()
    {
        // Set up directories
        $baseDir = dirname(__DIR__);
        $this->uploadsDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' .
            DIRECTORY_SEPARATOR . 'admissions' . DIRECTORY_SEPARATOR;
        $this->pdfDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' .
            DIRECTORY_SEPARATOR . 'pdf_admissions' . DIRECTORY_SEPARATOR;

        // Create directories with error handling
        $this->ensureDirectoryExists($this->uploadsDir);
        $this->ensureDirectoryExists($this->pdfDir);

        // Set custom temp directory for PhpWord
        $tempDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' .
            DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        $this->ensureDirectoryExists($tempDir);

        Settings::setTempDir($tempDir);
        // Configure PDF renderer
        Settings::setPdfRendererPath($baseDir . '/vendor/dompdf/dompdf');
        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
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
            // Generate DOCX file first
            $docxFile = $this->generateDocxLetter($applicationData);

            // Convert to PDF and store it
            $pdfFile = $this->convertToPDF($docxFile, $applicationData);

            return [
                'docx' => $docxFile,
                'pdf' => $pdfFile
            ];
        } catch (Exception $e) {
            error_log("Document generation error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    private function generateDocxLetter($applicationData)
    {
        // Select template based on study mode
        $studyMode = strtolower($applicationData['study_mode'] ?? '');
        $isDistance = in_array($studyMode, ['distance', 'online'], true);
        $templatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' .
            DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
            ($isDistance ? 'distance_admission.docx' : 'fulltime_admission.docx');

        error_log("Using template: " . $templatePath);

        if (!file_exists($templatePath)) {
            throw new Exception('Template file not found: ' . $templatePath);
        }

        // Load template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace variables in template
        $templateProcessor->setValue('student_name', ($applicationData['firstname'] ?? '') . ' ' . ($applicationData['lastname'] ?? ''));
        $templateProcessor->setValue('student_contact', $applicationData['contact'] ?? '');
        $templateProcessor->setValue('student_email', $applicationData['email'] ?? '');
        $templateProcessor->setValue('student_number', $applicationData['student_id_number'] ?? '');
        $templateProcessor->setValue('G_ID', $applicationData['G_ID'] ?? '');
        $templateProcessor->setValue('student_study_mode', $applicationData['study_mode'] ?? '');
        $templateProcessor->setValue('student_nationality', $applicationData['nationality'] ?? '');
        $templateProcessor->setValue('tuition_fee', $applicationData['tuition_fee'] ?? '');
        $templateProcessor->setValue('student_duration', $applicationData['duration'] ?? '');
        $templateProcessor->setValue('recruiter_name', $applicationData['recruiter_name'] ?? '');
        $templateProcessor->setValue('student_program', $applicationData['program_name'] ?? '');
        $templateProcessor->setValue('admission_type', strtoupper($applicationData['admission_type'] ?? ''));
        $templateProcessor->setValue('date', date('d/m/Y'));
        $templateProcessor->setValue('date_of_registration', date('d/m/Y'));
        $templateProcessor->setValue('date_of_commencement', $applicationData['intake'] ?? '');
        $templateProcessor->setValue('level', $applicationData['level'] ?? '');
        $templateProcessor->setValue('duration', $applicationData['duration'] ?? '');
        // Create safe filename
        $safeName = preg_replace(
            '/[^a-zA-Z0-9]/',
            '_',
            strtolower($applicationData['firstname'] . '_' . $applicationData['lastname'])
        );

        $fileName = $safeName . '_' . date('Y_m_d') . '.docx';
        $outputFile = $this->uploadsDir . $fileName;

        error_log("Saving DOCX to: " . $outputFile);
        $templateProcessor->saveAs($outputFile);

        return $outputFile;
    }

    private function convertToPDF($docxFile, $applicationData)
    {
        // Windows path to LibreOffice
        $libreOfficePath = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';

        $command = sprintf(
            '%s --headless --convert-to pdf --outdir "%s" "%s"',
            $libreOfficePath,
            $this->pdfDir,
            $docxFile
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception("PDF conversion failed");
        }

        $pdfFile = $this->pdfDir . pathinfo($docxFile, PATHINFO_FILENAME) . '.pdf';
        return $pdfFile;
    }
}
