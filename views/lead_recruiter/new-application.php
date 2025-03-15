<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/StudyMode.php';

checkAuth(['recruiter', 'lead_recruiter']);

// Initialize models
$studyMode = new StudyMode();
$database = new Database();
$conn = $database->getConnection();

try {
    // Get active programs
    $stmt = $conn->query("SELECT id, name FROM programs WHERE status = 'active' ORDER BY name");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug study modes
    $study_modes = $studyMode->getAllModes();
    error_log("Study modes result: " . print_r($study_modes, true));

    if (!$study_modes) {
        // Try direct database query for debugging
        $stmt = $conn->query("SELECT COUNT(*) as count FROM study_modes");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Number of study modes in database: " . $count['count']);
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    error_log($error);
}

// Add visual debugging
echo "<!-- Debug: Study modes: ";
var_export($study_modes);
echo " -->";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
   

    /* Main content adjustment */
    .main-content {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 2rem;
    }

    /* Form card styles */
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    @media (min-width: 768px) {
        .form-card {
            padding: 2rem;
        }
    }

    /* Section styles */
    .section-header {
        background-color: var(--primary-color);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        font-weight: 500;
    }

    /* Form controls */
    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.625rem 0.75rem;
        transition: all 0.2s ease-in-out;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
    }

    .input-group {
        border-radius: 8px;
        overflow: hidden;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group .form-select {
        border-right: none;
    }

    /* Required field indicator */
    .required::after {
        content: "*";
        color: #dc3545;
        margin-left: 4px;
        font-weight: bold;
    }

    /* Card styles */
    .card {
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, .125);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Attachment section */
    .attachment-item .btn-danger {
        width: 38px;
        height: 38px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .attachment-item .btn-danger:hover {
        transform: scale(1.1);
    }

    /* Submit button */
    .submit-btn {
        padding: 0.75rem 2rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Alert styles */
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }


    /* Sidebar toggle button */
    .sidebar-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Close button for sidebar on mobile */
    .sidebar-close {
        display: none;
        /* Hidden by default */
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6c757d;
        cursor: pointer;
        z-index: 1001;
        /* Ensure it's above the sidebar */
    }

    @media (max-width: 767.98px) {
        .sidebar-close {
            display: block;
            /* Show on mobile */
        }
    }

    @media (max-width: 767.98px) {
        .sidebar-toggle {
            display: block;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }
    }

    /* Responsive grid adjustments */
    @media (max-width: 768px) {

        .col-md-4,
        .col-md-6 {
            margin-bottom: 1rem;
        }

        .attachment-item .row {
            margin-bottom: 1rem;
        }

        .attachment-item .btn-danger {
            margin-top: 1rem;
        }
    }

    /* Loading spinner */
    .loading-spinner {
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 0.5rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../../includes/lead_recruiter_sidebar.php'; ?>



            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <?php include '../../includes/lead_recruiter_header.php'; ?>
                <div class="form-card">
                    <h4 class="mb-4 text-primary">New Application</h4>
                    <form id="applicationForm" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <!-- Personal Information -->
                        <div class="section-header">
                            <i class="bi bi-person-fill me-2"></i>Personal Information
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="firstname" class="form-label required">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middlename" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middlename" name="middlename">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lastname" class="form-label required">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact" class="form-label required">Contact Number</label>
                                <div class="input-group">
                                    <select class="form-select" id="country_code" name="country_code"
                                        style="max-width: 150px;" required>
                                        <option value="">Code</option>
                                        <!-- Africa -->
                                        <option value="+260">+260 (Zambia)</option>
                                        <option value="+263">+263 (Zimbabwe)</option>
                                        <option value="+27">+27 (South Africa)</option>
                                        <option value="+267">+267 (Botswana)</option>
                                        <option value="+244">+244 (Angola)</option>
                                        <option value="+264">+264 (Namibia)</option>
                                        <option value="+266">+266 (Lesotho)</option>
                                        <option value="+268">+268 (Eswatini)</option>
                                        <option value="+243">+243 (DR Congo)</option>
                                        <option value="+255">+255 (Tanzania)</option>
                                        <option value="+256">+256 (Uganda)</option>
                                        <option value="+254">+254 (Kenya)</option>
                                        <option value="+250">+250 (Rwanda)</option>
                                        <option value="+257">+257 (Burundi)</option>
                                        <option value="+258">+258 (Mozambique)</option>
                                        <option value="+251">+251 (Ethiopia)</option>
                                        <option value="+249">+249 (Sudan)</option>
                                        <option value="+211">+211 (South Sudan)</option>
                                        <option value="+234">+234 (Nigeria)</option>
                                        <option value="+233">+233 (Ghana)</option>
                                        <option value="+225">+225 (Côte d'Ivoire)</option>
                                        <option value="+237">+237 (Cameroon)</option>
                                        <option value="+241">+241 (Gabon)</option>
                                        <option value="+242">+242 (Congo)</option>
                                        <option value="+229">+229 (Benin)</option>
                                        <option value="+228">+228 (Togo)</option>
                                        <option value="+227">+227 (Niger)</option>
                                        <option value="+226">+226 (Burkina Faso)</option>
                                        <option value="+223">+223 (Mali)</option>
                                        <option value="+222">+222 (Mauritania)</option>
                                        <option value="+220">+220 (Gambia)</option>
                                        <option value="+216">+216 (Tunisia)</option>
                                        <option value="+213">+213 (Algeria)</option>
                                        <option value="+212">+212 (Morocco)</option>
                                        <option value="+236">+236 (Central African Republic)</option>
                                        <option value="+235">+235 (Chad)</option>
                                        <option value="+232">+232 (Sierra Leone)</option>
                                        <option value="+231">+231 (Liberia)</option>
                                        <option value="+230">+230 (Mauritius)</option>
                                        <option value="+239">+239 (São Tomé & Príncipe)</option>
                                        <option value="+238">+238 (Cape Verde)</option>
                                        <option value="+248">+248 (Seychelles)</option>
                                        <option value="+247">+247 (Ascension)</option>
                                        <option value="+246">+246 (Diego Garcia)</option>
                                        <option value="+245">+245 (Guinea-Bissau)</option>
                                        <option value="+240">+240 (Equatorial Guinea)</option>
                                        <!-- Rest of World -->
                                        <option value="+1">+1 (USA/Canada)</option>
                                        <option value="+44">+44 (UK)</option>
                                        <option value="+86">+86 (China)</option>
                                        <option value="+91">+91 (India)</option>
                                        <option value="+7">+7 (Russia)</option>
                                        <option value="+81">+81 (Japan)</option>
                                        <option value="+49">+49 (Germany)</option>
                                        <option value="+33">+33 (France)</option>
                                        <option value="+61">+61 (Australia)</option>
                                        <option value="+55">+55 (Brazil)</option>
                                        <option value="+52">+52 (Mexico)</option>
                                        <option value="+39">+39 (Italy)</option>
                                        <option value="+34">+34 (Spain)</option>
                                        <option value="+82">+82 (South Korea)</option>
                                        <option value="+971">+971 (UAE)</option>
                                    </select>
                                    <input type="tel" class="form-control" id="contact" name="contact"
                                        placeholder="Phone number" required>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid contact number
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nationality" class="form-label required">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ID Number" class="form-label required">
                                    ID Number</label>
                                <input type="text" class="form-control" id="id_number" name="id_number" required>
                            </div>
                        </div>

                        <!-- School Selection -->
                        <div class="section-header">
                            <i class="bi bi-building me-2"></i>School Selection
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="school_id" class="form-label required">School</label>
                                <select class="form-select" id="school_id" name="school_id" required>
                                    <option value="">Select School</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a school
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="intake_id" class="form-label required">Intake</label>
                                <select class="form-select" id="intake_id" name="intake_id" required>
                                    <option value="">Select Intake</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select an intake
                                </div>
                            </div>
                        </div>

                        <!-- Program Selection -->
                        <div class="section-header">
                            <i class="bi bi-file-earmark-text me-2"></i>Program Selection
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="program_id" class="form-label required">Program</label>
                                <select class="form-select" id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a program
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="study_mode_id" class="form-label required">Study Mode</label>
                                <select class="form-select" id="study_mode_id" name="study_mode_id" required>
                                    <option value="">Select Study Mode</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a study mode
                                </div>
                            </div>
                        </div>

                        <!-- Qualification Attachments -->
                        <div class="section-header">
                            <i class="bi bi-file-earmark-text me-2"></i>Qualification Attachments
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Student Attachments</h5>
                                <button type="button" class="btn btn-sm btn-success" onclick="addAttachment()">
                                    <i class="bi bi-plus-circle"></i> Add more attachments
                                </button>
                            </div>
                            <div id="attachments-container">
                                <div class="attachment-item mb-3" data-attachment-id="1">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label class="form-label required">Document Type</label>
                                                    <select class="form-select" name="attachment_type[]" required>
                                                        <option value="">Select Type</option>
                                                        <option value="1">Certificate</option>
                                                        <option value="2">Transcript</option>
                                                        <option value="3">National ID</option>
                                                        <option value="4">Passport</option>
                                                        <option value="5">Other</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label required">Document</label>
                                                    <input type="file" class="form-control" name="attachment_file[]"
                                                        required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="removeAttachment(this)" style="display: none;">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admission Type -->
                        <div class="section-header">
                            <i class="bi bi-mortarboard me-2"></i>Admission Type
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="admission_id" class="form-label required">Admission Type</label>
                                <select class="form-select" id="admission_id" name="admission_id" required>
                                    <option value="">Select Admission Type</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select an admission type
                                </div>
                            </div>
                        </div>
                        <div id="alert-container"></div>
                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary submit-btn"
                                    onclick="window.history.back()">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary submit-btn">
                                    <i class="bi bi-check-circle me-2"></i>Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const schoolSelect = document.getElementById('school_id');
        const programSelect = document.getElementById('program_id');
        const intakeSelect = document.getElementById('intake_id');
        const admissionSelect = document.getElementById('admission_id');

        
        // Load schools
        fetch('../../api/schools/get.php')
            .then(response => response.json())
            .then(data => {
                if (data.status && data.data) {
                    data.data.forEach(school => {
                        const option = new Option(school.school_name, school.id);
                        schoolSelect.add(option);
                    });
                } else {
                    showAlert('danger', 'Failed to load schools');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load schools');
            });

        // Load programs based on selected school
        schoolSelect.addEventListener('change', function() {
            // Clear existing programs
            programSelect.innerHTML = '<option value="">Select Program</option>';

            if (!this.value) {
                return;
            }

            // Show loading state
            programSelect.disabled = true;
            const loadingOption = new Option('Loading programs...', '');
            programSelect.add(loadingOption);

            fetch(`../../api/programs/get.php?school_id=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    // Remove loading option
                    programSelect.remove(programSelect.options.length - 1);
                    programSelect.disabled = false;

                    if (data.status && data.data) {
                        if (data.data.length === 0) {
                            const noPrograms = new Option('No programs available', '');
                            noPrograms.disabled = true;
                            programSelect.add(noPrograms);
                        } else {
                            data.data.forEach(program => {
                                const option = new Option(program.program_name, program.id);
                                programSelect.add(option);
                            });
                        }
                    } else {
                        showAlert('danger', 'Failed to load programs');
                    }
                })
                .catch(error => {
                    programSelect.disabled = false;
                    programSelect.innerHTML = '<option value="">Select Program</option>';
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to load programs');
                });
        });

        // Load study modes
        fetch('../../api/study-modes/get.php')
            .then(response => response.json())
            .then(data => {
                if (data.status && data.data) {
                    const modeSelect = document.getElementById('study_mode_id');
                    data.data.forEach(mode => {
                        const option = new Option(mode.mode_name, mode.id);
                        modeSelect.add(option);
                    });
                } else {
                    showAlert('danger', 'Failed to load study modes');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load study modes');
            });

        // Load intakes
        fetch('../../api/intakes/get.php')
            .then(response => response.json())
            .then(data => {
                console.log(data);

                if (data.status && data.data) {
                    data.data.forEach(intake => {
                        const option = new Option(intake.intake_description, intake.id);
                        intakeSelect.add(option);
                    });
                } else {
                    showAlert('danger', 'Failed to load intakes');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load intakes');
            });

        // Load admission types
        fetch('../../api/admission-types/get.php')
            .then(response => response.json())
            .then(data => {
                console.log('Admission types:', data);
                if (data.status && data.data) {
                    data.data.forEach(admission => {
                        const option = new Option(admission.admission_description, admission.id);
                        admissionSelect.add(option);
                    });
                } else {
                    showAlert('danger', 'Failed to load admission types');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load admission types');
            });

        // Add this function to validate phone numbers
        function validatePhoneNumber(countryCode, number) {
            // Remove all spaces and non-digit characters except +
            const cleanNumber = number.replace(/[^\d]/g, '');

            // Basic validation rules for different country codes
            const validations = {
                '+260': {
                    length: 9
                }, // Zambia
                '+263': {
                    length: 9
                }, // Zimbabwe
                '+27': {
                    length: 9
                }, // South Africa
                '+267': {
                    length: 8
                }, // Botswana
                // Add more country-specific validations as needed
            };

            const validation = validations[countryCode];
            if (validation) {
                return cleanNumber.length === validation.length;
            }

            // Default validation if country code is not in the list
            return cleanNumber.length >= 8 && cleanNumber.length <= 15;
        }

        // Update form submission to include phone validation
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            const formData = new FormData(this);

            // Validate phone number
            const countryCode = formData.get('country_code');
            const contactNumber = formData.get('contact');

            if (!countryCode) {
                showAlert('danger', 'Please select a country code');
                return;
            }

            if (!validatePhoneNumber(countryCode, contactNumber)) {
                showAlert('danger', 'Please enter a valid phone number for the selected country');
                return;
            }

            // Combine country code and contact number
            const fullContact = countryCode + contactNumber.replace(/\s/g, '');
            formData.set('contact', fullContact);

            // Validate required fields
            const requiredFields = [
                'firstname', 'lastname', 'email', 'contact',
                'nationality', 'id_number', 'school_id',
                'program_id', 'study_mode_id', 'intake_id',
                'admission_id'
            ];

            for (const field of requiredFields) {
                if (!formData.get(field)) {
                    showAlert('danger', `Please fill in all required fields`);
                    return;
                }
            }

            // Check if at least one attachment is present
            const attachmentTypes = formData.getAll('attachment_type[]');
            const attachmentFiles = document.querySelectorAll('input[type="file"]');

            if (attachmentTypes.length === 0 || !attachmentFiles[0].files[0]) {
                showAlert('danger', 'Please add at least one document attachment');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                    <span class="loading-spinner"></span>
                    Submitting...
                `;

            fetch('../../api/applications/create.php', {
                    method: 'POST',
                    body: formData
                })
                .then(async response => {
                    const text = await response.text();
                    try {
                        const jsonStr = text.substring(text.indexOf('{'));
                        return JSON.parse(jsonStr);
                    } catch (e) {
                        console.error('Raw response:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                })
                .then(data => {
                    if (data.status) {
                        showAlert('success', 'Application submitted successfully!');
                        setTimeout(() => {
                            // window.location.reload();
                            console.log('Application submitted successfully!', data);
                        }, 1500);
                    } else {
                        showAlert('danger', data.message || 'Failed to submit application');
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showAlert('danger', 'An error occurred. Please try again.');
                });
        });
    });

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
    }

    function logout() {
        fetch('../../api/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    localStorage.removeItem('jwt_token');
                    window.location.href = '../login.php';
                }
            })
            .catch(error => {
                console.error('Logout failed:', error);
            });
    }

    let attachmentCount = 1;

    function addAttachment() {
        attachmentCount++;
        1
        const container = document.getElementById('attachments-container');
        const newAttachment = document.createElement('div');
        newAttachment.className = 'attachment-item mb-3';
        newAttachment.dataset.attachmentId = attachmentCount;

        newAttachment.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label required">Document Type</label>
                                <select class="form-select" name="attachment_type[]" required>
                                    <option value="">Select Type</option>
                                    <option value="1">Certificate</option>
                                    <option value="2">Transcript</option>
                                    <option value="3">National ID</option>
                                    <option value="4">Passport</option>
                                    <option value="5">Other</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label required">Document</label>
                                <input type="file" class="form-control" name="attachment_file[]" required 
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" onclick="removeAttachment(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

        container.appendChild(newAttachment);
        updateRemoveButtons();
    }

    function removeAttachment(button) {
        const attachmentItem = button.closest('.attachment-item');
        attachmentItem.remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const attachments = document.querySelectorAll('.attachment-item');
        attachments.forEach(attachment => {
            const removeButton = attachment.querySelector('.btn-danger');
            if (attachments.length === 1) {
                removeButton.style.display = 'none';
            } else {
                removeButton.style.display = 'flex';
            }
        });
    }
    </script>
</body>

</html>