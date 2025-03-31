<?php
require('conn.php');
require '../vendor/autoload.php'; // Ensure PhpWord and Dompdf are autoloaded

use PhpOffice\PhpWord\TemplateProcessor;

// Set the folder to store the generated PDFs
$provisionFolder = 'provisions/';
if (!is_dir($provisionFolder)) {
    mkdir($provisionFolder, 0755, true); // Create directory if it doesn't exist
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File upload handling
$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true); // Create directory if it doesn't exist
}

// Function to generate the student ID
function generate_student_id($row_id)
{
    $year = date("Y"); // Current year
    $month = date("m"); // Current month (2 digits)
    $id_part = str_pad($row_id, 2, '0', STR_PAD_LEFT); // 2-digit row ID with leading zero if needed
    return $year . $month . $id_part; // Concatenate year, month, and row ID
}

// Check if a file was uploaded
if (isset($_FILES['qualification_file'])) {
    $file = $_FILES['qualification_file'];
    $fileName = basename($file['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow only certain file formats (optional)
    $allowedTypes = ['pdf'];
    if (!in_array(strtolower($fileType), $allowedTypes)) {
        echo json_encode(['error' => 'Invalid file type. Only PDF files are allowed.']);
        exit();
    }

    // Move uploaded file to the destination
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        // Retrieve form data and sanitize
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $surname = mysqli_real_escape_string($conn, $_POST['surname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $level = mysqli_real_escape_string($conn, $_POST['level']);
        $program = mysqli_real_escape_string($conn, $_POST['program']);
        $intake = mysqli_real_escape_string($conn, $_POST['intake']);
        $mode = mysqli_real_escape_string($conn, $_POST['mode']);
        $enrollmentType = mysqli_real_escape_string($conn, $_POST['enrollment_type']);
        $recruiterFname = mysqli_real_escape_string($conn, $_POST['recruiter_fname']);
        $recruiterSurname = mysqli_real_escape_string($conn, $_POST['recruiter_surname']);
        $recruiterEmail = mysqli_real_escape_string($conn, $_POST['recruiter_email']);
        $statusId = 1; // Default status is 'In Progress'

        // Insert form data into the database (without student_id for now)
        $sql = "INSERT INTO applications (
                    first_name, surname, email, phone, level, program, intake, mode, 
                    enrollment_type, recruiter_fname, recruiter_surname, recruiter_email, 
                    qualification_file, status_id
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            echo json_encode(['error' => 'Failed to prepare statement: ' . mysqli_error($conn)]);
            exit();
        }

        // Bind parameters
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssssi",
            $firstName,
            $surname,
            $email,
            $phone,
            $level,
            $program,
            $intake,
            $mode,
            $enrollmentType,
            $recruiterFname,
            $recruiterSurname,
            $recruiterEmail,
            $fileName,
            $statusId
        );

        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            // Get the last inserted row ID
            $last_inserted_id = mysqli_insert_id($conn);

            // Generate the student ID
            $student_id = generate_student_id($last_inserted_id);

            // Update the row with the generated student ID
            $update_sql = "UPDATE applications SET student_id = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            if (!$update_stmt) {
                echo json_encode(['error' => 'Failed to prepare update statement: ' . mysqli_error($conn)]);
                exit();
            }

            // Bind the new student_id and the row ID
            mysqli_stmt_bind_param($update_stmt, "si", $student_id, $last_inserted_id);


            // Execute the update
            if (mysqli_stmt_execute($update_stmt)) {
                // Fetch the data for PDF generation
                $sql2 = "SELECT * FROM application_details WHERE id = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, 'i', $last_inserted_id);
                mysqli_stmt_execute($stmt2);
                $result2 = mysqli_stmt_get_result($stmt2);

                if ($row2 = mysqli_fetch_assoc($result2)) {
                    // Load the Word template
                    $template = new TemplateProcessor('temp.docx');

                    // Determine the correct amount based on the mode of study
                    $amount = '';
                    $reg_fee = '';
                    $identity_fee = '';
                    $internet_fee = '';
                    $library_fee = '';
                    $maintenance_fee = '';
                    $medical_fee = '';
                    $club_fee = '';
                    $sports_fee = '';
                    $student_org_fee = '';
                    $practical_lab_fee = '';
                    $practical_healthy_fee = '';
                    $examination_fee = '';
                    $supplementary_fee = '';

                    $date_reg = date('Y-m-d', strtotime('+1 month'));
                    $date_commencement = 'Upon First Payment';
                    if ($row2['mode'] == 'Online learning' || $row2['mode'] == 'Distance Learning') {
                        $amount = $row2['online_odl_semester'];
                    } elseif ($row2['mode'] == 'Full Time') {
                        $amount = $row2['full_time_semester'];

                        if ($intake == 'October') {
                            $date_commencement = '2024-10-18';
                        } elseif ($intake == 'January') {
                            $date_commencement = '2025-01-23';
                        } elseif ($intake == 'April') {
                            $date_commencement = 'TBA';
                        } elseif ($intake == 'June') {
                            $date_commencement = 'TBA';
                        }
                    } else {
                        $amount = 'Unknown Mode';
                    }
                    // Determine the correct years based on the programe_type of study
                    $years = '';
                    $year_words = '';
                    if ($row2['program_type'] == 'Bachelor') {
                        $years = 4;
                        $year_words = 'Four';

                        if ($row2['mode'] == 'Online learning' || $row2['mode'] == 'Distance Learning') {
                            $reg_fee = '250';
                            $identity_fee = '100';
                            $internet_fee = '-';
                            $library_fee = '100';
                            $maintenance_fee = '-';
                            $medical_fee = '-';
                            $club_fee = '-';
                            $sports_fee = '-';
                            $student_org_fee = '-';
                            $practical_lab_fee = '-';
                            $practical_healthy_fee = '-';
                            $examination_fee = '150';
                            $supplementary_fee = '250';
                        }
                        if ($row2['mode'] == 'Full Time') {
                            $reg_fee = '250';
                            $identity_fee = '100';
                            $internet_fee = '200';
                            $library_fee = '100';
                            $maintenance_fee = '150';
                            $medical_fee = '250';
                            $club_fee = '150';
                            $sports_fee = '300';
                            $student_org_fee = '50';
                            $practical_lab_fee = '200';
                            $practical_healthy_fee = '300';
                            $examination_fee = '150';
                            $supplementary_fee = '250';
                        }
                    } elseif ($row2['program_type'] == 'Diploma') {
                        $years = 3;
                        $year_words = 'Three';
                        if ($row2['mode'] == 'Online learning' || $row2['mode'] == 'Distance Learning') {
                            $reg_fee = '250';
                            $identity_fee = '100';
                            $internet_fee = '-';
                            $library_fee = '100';
                            $maintenance_fee = '-';
                            $medical_fee = '-';
                            $club_fee = '-';
                            $sports_fee = '-';
                            $student_org_fee = '-';
                            $practical_lab_fee = '-';
                            $practical_healthy_fee = '-';
                            $examination_fee = '150';
                            $supplementary_fee = '250';
                        }
                        if ($row2['mode'] == 'Full Time') {
                            $reg_fee = '250';
                            $identity_fee = '100';
                            $internet_fee = '200';
                            $library_fee = '100';
                            $maintenance_fee = '150';
                            $medical_fee = '250';
                            $club_fee = '150';
                            $sports_fee = '300';
                            $student_org_fee = '50';
                            $practical_lab_fee = '200';
                            $practical_healthy_fee = '300';
                            $examination_fee = '150';
                            $supplementary_fee = '250';
                        }
                    } elseif ($row2['program_type'] == 'Master') {
                        $years = 2;
                        $year_words = 'Two';

                        if ($row2['mode'] == 'Online learning' || $row2['mode'] == 'Distance Learning') {
                            $reg_fee = '500';
                            $identity_fee = '200';
                            $internet_fee = '-';
                            $library_fee = '500';
                            $maintenance_fee = '-';
                            $medical_fee = '-';
                            $club_fee = '-';
                            $sports_fee = '-';
                            $student_org_fee = '-';
                            $practical_lab_fee = '-';
                            $practical_healthy_fee = '-';
                            $examination_fee = '250';
                            $supplementary_fee = '350';
                        }
                        if ($row2['mode'] == 'Full Time') {
                            $reg_fee = '500';
                            $identity_fee = '200';
                            $internet_fee = '200';
                            $library_fee = '500';
                            $maintenance_fee = '150';
                            $medical_fee = '250';
                            $club_fee = '150';
                            $sports_fee = '300';
                            $student_org_fee = '50';
                            $practical_lab_fee = '200';
                            $practical_healthy_fee = '300';
                            $examination_fee = '250';
                            $supplementary_fee = '350';
                        }
                    }





                    // Assuming $program_type is defined and represents the type of program
                    if ($row2['program_type'] == 'Online learning' || $row2['program_type'] == 'Distance Learning') {
                        $amount = 1000; // Example amount for online or distance learning
                        $reg_fee = 100;
                        $identity_fee = 50;
                        $internet_fee = 75;
                        $library_fee = 60;
                        $maintenance_fee = 80;
                        $medical_fee = 90;
                        $club_fee = 40;
                        $sports_fee = 35;
                        $student_org_fee = 20;
                        $practical_lab_fee = 0; // Assume no practical labs for online learning
                        $practical_healthy_fee = 0;
                        $examination_fee = 150;
                        $supplementary_fee = 50;

                    } elseif ($row2['program_type'] == 'Full Time') {
                        $amount = 2000; // Example amount for full-time learning
                        $reg_fee = 200;
                        $identity_fee = 100;
                        $internet_fee = 100;
                        $library_fee = 120;
                        $maintenance_fee = 150;
                        $medical_fee = 130;
                        $club_fee = 80;
                        $sports_fee = 60;
                        $student_org_fee = 50;
                        $practical_lab_fee = 300;
                        $practical_healthy_fee = 100;
                        $examination_fee = 200;
                        $supplementary_fee = 100;

                    } elseif ($row2['program_type'] == 'Masters') {
                        $amount = 3000; // Example amount for masters program
                        $reg_fee = 300;
                        $identity_fee = 150;
                        $internet_fee = 120;
                        $library_fee = 150;
                        $maintenance_fee = 200;
                        $medical_fee = 170;
                        $club_fee = 100;
                        $sports_fee = 80;
                        $student_org_fee = 70;
                        $practical_lab_fee = 400;
                        $practical_healthy_fee = 200;
                        $examination_fee = 250;
                        $supplementary_fee = 150;

                    } else {
                        echo "Invalid program type";
                    }

                    // You can now use these variables in the rest of your application, such as setting values for a template processor.




                    // Populate the template with values
                    $template->setValue('name', $row2['first_name'] . ' ' . $row2['surname']);
                    $template->setValue('email', $row2['email']);
                    $template->setValue('mode', $row2['mode']);
                    $template->setValue('program', $row2['program_name']);
                    $template->setValue('student_id', $row2['student_id']);
                    $template->setValue('date', date('Y-m-d'));
                    $template->setValue('date_reg', $date_reg);
                    $template->setValue('date_commencement', $date_commencement);
                    $template->setValue('phone', $row2['phone']);
                    $template->setValue('amount', $amount); // Example amount
                    $template->setValue('reg_fee', $reg_fee);
                    $template->setValue('identity_fee', $identity_fee);
                    $template->setValue('internet_fee', $internet_fee);
                    $template->setValue('library_fee', $library_fee);
                    $template->setValue('maintenance_fee', $maintenance_fee);
                    $template->setValue('medical_fee', $medical_fee);
                    $template->setValue('club_fee', $club_fee);
                    $template->setValue('sports_fee', $sports_fee);
                    $template->setValue('student_org_fee', $student_org_fee);
                    $template->setValue('practical_lab_fee', $practical_lab_fee);
                    $template->setValue('practical_healthy_fee', $practical_healthy_fee);
                    $template->setValue('examinatioin_fee', $examination_fee);
                    $template->setValue('supplementary_fee', $supplementary_fee);
                    $template->setValue('year', $years); // Example value for years
                    $template->setValue('year_words', $year_words); // Example value for years
                    $template->setValue('recruiter', $row2['recruiter_fname'] . ' ' . $row2['recruiter_surname']); // Example value for years

                    // Save the modified Word document to a temporary file
                    $tempWordFile = tempnam(sys_get_temp_dir(), 'word') . '.docx';
                    $template->saveAs($tempWordFile);

                    // Define the final PDF file name
                    $pdfFileName = $provisionFolder . 'provision_' . $row2['first_name'] . '_' . $row2['surname'] . '_' . $row2['student_id'] . '.pdf';

                    // Generate the PDF using LibreOffice
                    $libreOfficeCommand = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf ' . escapeshellarg($tempWordFile) . ' --outdir ' . escapeshellarg(realpath($provisionFolder));
                    exec($libreOfficeCommand . ' 2>&1', $output, $return_var);

                    if ($return_var === 0) {
                        // Rename or move the generated PDF file to the desired file name
                        $generatedPdfFile = $provisionFolder . pathinfo($tempWordFile, PATHINFO_FILENAME) . '.pdf';
                        if (rename($generatedPdfFile, $pdfFileName)) {
                            echo "PDF generated successfully and saved as $pdfFileName";
                        
                            // Update the database with the new PDF file name
                            $update_sql2 = "UPDATE applications SET provision_letter = ? WHERE id = ?";
                            $update_stmt2 = mysqli_prepare($conn, $update_sql2);
                            
                            if (!$update_stmt2) {
                                echo json_encode(['error' => 'Failed to prepare update statement: ' . mysqli_error($conn)]);
                                exit();
                            }
                        
                            // Bind the file name and the row ID
                            mysqli_stmt_bind_param($update_stmt2, "si", $pdfFileName, $last_inserted_id);
                        
                            // Execute the update query
                            if (mysqli_stmt_execute($update_stmt2)) {
                                echo "Provision letter field updated successfully.";
                            } else {
                                echo json_encode(['error' => 'Failed to update provision_letter: ' . mysqli_error($conn)]);
                            }
                        
                            // Close the statement after execution
                            mysqli_stmt_close($update_stmt2);
                        
                        } else {
                            echo "PDF generated, but failed to rename the file.";
                        }
                        
                    } else {
                        echo "Failed to convert the document to PDF. Command output: " . implode("\n", $output);
                    }

                    // Optionally, delete the temporary Word file
                    unlink($tempWordFile);
                } else {
                    echo "No data found for the application.";
                }

                // Close the statement
                mysqli_stmt_close($stmt2);

            } else {
                echo json_encode(['error' => 'Failed to update student ID: ' . mysqli_error($conn)]);
            }

            // Close the update statement
            mysqli_stmt_close($update_stmt);

        } else {
            echo json_encode(['error' => 'Failed to execute statement: ' . mysqli_error($conn)]);
        }

        // Close the original statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        echo json_encode(['error' => 'File upload failed.']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded.']);
}