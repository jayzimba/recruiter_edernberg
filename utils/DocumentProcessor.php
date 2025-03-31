<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class DocumentProcessor
{
    private $uploadsDir;
    private $pdfDir;
    private $libreOfficePath;

    public function __construct()
    {
        // Set up directories
        $baseDir = dirname(__DIR__);
        $this->uploadsDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' .
            DIRECTORY_SEPARATOR . 'admissions' . DIRECTORY_SEPARATOR;
        $this->pdfDir = $baseDir . DIRECTORY_SEPARATOR . 'uploads' .
            DIRECTORY_SEPARATOR . 'pdf_admissions' . DIRECTORY_SEPARATOR;

        // Update LibreOffice path to match sample.php
        $this->libreOfficePath = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';

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

        // Add this to your constructor after creating directories
        foreach ([$this->uploadsDir, $this->pdfDir] as $dir) {
            chmod($dir, 0777);
        }
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
            
            // Convert to PDF using LibreOffice
            $pdfFile = $this->convertToPDF($docxFile);

            // Return both file paths
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

        //check if $applicationData['level'] is Masters to use masters template
        $isMasters = $applicationData['level'] === 'Masters';


        if($isMasters){
            $templatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' .
            DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
            'masters_admission.docx';
        }else{
            //go further to check if the program is either  "Diploma in Clinical Medicine" or "Diploma in Registered Nursing" use this templtate Nursing_clinical_medicine_admission.docx   
            if($applicationData['program_name'] === 'Diploma in Clinical Medicine' || $applicationData['program_name'] === 'Diploma in Registered Nursing'){
                $templatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' .
                DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
                'Nursing_clinical_medicine_admission.docx';
            }else{
                $templatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' .
                DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .
                ($isDistance ? 'distance_admission.docx' : 'fulltime_admission.docx');
            }
        }

        
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
        sleep(1); // Wait for file to be fully written

        return $outputFile;
    }

    private function convertToPDF($docxFile)
    {
        try {
            // Get the directory and filename
            $pdfDir = $this->pdfDir;
            $filename = pathinfo($docxFile, PATHINFO_FILENAME);

            // Ensure the PDF directory exists and is writable
            $this->ensureDirectoryExists($pdfDir);

            // Verify source file exists
            if (!file_exists($docxFile)) {
                throw new Exception("Source DOCX file not found: $docxFile");
            }

            // Define the final PDF file name
            $pdfFileName = $pdfDir . $filename . '.pdf';

            // Kill any existing LibreOffice processes
            exec('taskkill /F /IM soffice.exe /T 2>&1', $killOutput, $killReturn);
            error_log("Kill LibreOffice processes output: " . print_r($killOutput, true));

            // Generate the PDF using LibreOffice - using the same command format as sample.php
            $libreOfficeCommand = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf ' . 
                escapeshellarg($docxFile) . ' --outdir ' . escapeshellarg(realpath($pdfDir));

            // Log the command
            error_log("Attempting PDF conversion with command: " . $libreOfficeCommand);
            error_log("Source DOCX exists: " . (file_exists($docxFile) ? 'Yes' : 'No'));
            error_log("Source DOCX size: " . filesize($docxFile) . " bytes");

            // Execute the command
            exec($libreOfficeCommand . ' 2>&1', $output, $return_var);

            // Log the output
            error_log("Command output: " . print_r($output, true));
            error_log("Return code: " . $return_var);

            if ($return_var === 0) {
                // Get the generated PDF file path
                $generatedPdfFile = $pdfDir . $filename . '.pdf';
                
                // Check if the PDF was created
                if (!file_exists($generatedPdfFile)) {
                    throw new Exception("PDF file was not created at expected location: $generatedPdfFile");
                }

                // Check if the PDF is not empty
                if (filesize($generatedPdfFile) === 0) {
                    throw new Exception("PDF file was created but is empty: $generatedPdfFile");
                }

                // Wait a short time to ensure file is fully written
                sleep(1);

                return $generatedPdfFile;
            } else {
                throw new Exception("LibreOffice conversion failed. Command output: " . implode("\n", $output));
            }
        } catch (Exception $e) {
            error_log("PDF Conversion error: " . $e->getMessage());
            error_log("Full error details: " . print_r($e, true));
            throw new Exception("Failed to convert DOCX to PDF: " . $e->getMessage());
        }
    }

    public function testLibreOfficeInstallation() {
        try {
            $command = $this->libreOfficePath . ' --version';
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                return [
                    'status' => true,
                    'message' => 'LibreOffice is properly installed',
                    'version' => $output
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'LibreOffice test failed',
                    'error' => $output
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'LibreOffice test threw an exception',
                'error' => $e->getMessage()
            ];
        }
    }

    // Add this helper method to test LibreOffice directly
    public function testConversion($testFile = null)
    {
        try {
            if (!$testFile) {
                // Create a simple test DOCX file
                $testFile = $this->uploadsDir . 'test.docx';
                $templateProcessor = new TemplateProcessor(dirname(__DIR__) . '/assets/templates/fulltime_admission.docx');
                $templateProcessor->setValue('test', 'test');
                $templateProcessor->saveAs($testFile);
            }

            // Try to convert it
            $result = $this->convertToPDF($testFile);
            
            return [
                'status' => true,
                'message' => 'Test conversion successful',
                'source' => $testFile,
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'source' => $testFile ?? 'No test file created',
                'error' => $e->getTraceAsString()
            ];
        }
    }
}
