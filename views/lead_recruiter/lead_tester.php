<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./icons/edernberg.png">
    <title>Program Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css" rel="stylesheet"/>
    <style>
        body {
            background: linear-gradient(
                135deg,
                rgba(0, 32, 96, 0.8),  /* Dark blue */
                rgba(0, 108, 172, 0.7), /* Medium blue */
                rgba(66, 133, 244, 0.6)  /* Light blue */
            ),
            url('./icons/bg.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: #333;
            padding: 2rem 0;
        }
        .lead-form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        .lead-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0056b3, #007bff, #0056b3);
            animation: shimmer 2s infinite linear;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .logo-container {
            text-align: center;
            margin: -1rem -1rem 2rem -1rem;
            padding: 2rem;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .logo-container img {
            max-width: 180px;
            height: auto;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        .logo-container img:hover {
            transform: scale(1.05);
        }
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        }
        .form-header h2 {
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        .form-header p {
            font-size: 1rem;
            opacity: 0.8;
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .form-control, .form-select {
            border: 2px solid rgba(206, 212, 218, 0.8);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            height: 3.5rem;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #0056b3;
        }
        .form-floating label {
            padding: 1rem;
            color: #6c757d;
        }
        .required::after {
            content: "*";
            color: #dc3545;
            margin-left: 4px;
            font-weight: bold;
        }
        .btn-primary {
            padding: 1rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(45deg, #0056b3, #007bff);
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
            background: linear-gradient(45deg, #004494, #0056b3);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        /* Enhanced animations */
        .form-floating, .btn-primary {
            opacity: 0;
            animation: fadeInUp 0.5s ease forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Enhanced stagger animations */
        .form-header { animation-delay: 0.1s; }
        .form-floating:nth-child(1) { animation-delay: 0.2s; }
        .form-floating:nth-child(2) { animation-delay: 0.3s; }
        .form-floating:nth-child(3) { animation-delay: 0.4s; }
        .form-floating:nth-child(4) { animation-delay: 0.5s; }
        .form-floating:nth-child(5) { animation-delay: 0.6s; }
        .btn-primary { animation-delay: 0.7s; }
        /* Phone input customization */
        .iti {
            width: 100%;
            margin-bottom: 1rem;
        }
        .iti__flag-container:hover {
            background-color: #f8f9fa;
        }
        .iti__selected-flag {
            border-radius: 12px 0 0 12px;
        }
        /* Alert customization */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .lead-form-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            .form-header h2 {
                font-size: 1.5rem;
            }
            .logo-container img {
                max-width: 150px;
            }
        }
        /* Loading spinner enhancement */
        .spinner-border-sm {
            margin-right: 0.5rem;
            vertical-align: middle;
        }
        .footer {
            text-align: center;
            padding: 1rem;
            margin-top: -1rem;
            color: white;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: #e6e6e6;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lead-form-container">
            <div class="logo-container">
                <img src="./icons/image.png" alt="University Logo">
            </div>
            
            <div class="form-header">
                <h2>Start Your Journey</h2>
                <p class="text-muted">Fill in your details to get started with your application</p>
            </div>
            
            <form id="leadForm">
                <input type="hidden" id="recruiter_id" name="recruiter_id" 
                       value="<?php echo htmlspecialchars($_GET['rid'] ?? ''); ?>">
                
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                    <label for="fullName" class="required">Full Name</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" required>
                    <label for="email" class="required">Email Address</label>
                </div>

                <div class="mb-3">
                    <label for="phone" class="required">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="school" name="school" required>
                        <option value="">Select School</option>
                    </select>
                    <label for="school" class="required">School</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="program" name="program" required disabled>
                        <option value="">Select Program</option>
                    </select>
                    <label for="program" class="required">Program</label>
                </div>

                <div id="formFeedback"></div>

                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                    Submit Application
                </button>
            </form>
        </div>
    </div>

    <div class="footer">
        Powered by <a href="https://www.lampsyc.com" target="_blank">Lampsync Technologies Ltd</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSelect = document.getElementById('school');
            const programSelect = document.getElementById('program');

            // Initialize phone input
            const phoneInput = window.intlTelInput(document.querySelector("#phone"), {
                preferredCountries: ["ke", "ug", "tz"],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            });

            // Load schools
            fetch('https://uoe.lampsync.com/api/schools/get.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.data) {
                        data.data.forEach(school => {
                            const option = new Option(school.school_name, school.id);
                            schoolSelect.add(option);
                        });
                    } else {
                        showFeedback('danger', 'Failed to load schools');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFeedback('danger', 'Failed to load schools');
                });

            // Load programs when school is selected
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

                fetch(`https://uoe.lampsync.com/api/programs/get.php?school_id=${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Programs data:', data);
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
                                    const option = new Option(program.program_name + ' - (' + program.mode_name + ')', program.id);
                                    programSelect.add(option);
                                });
                            }
                        } else {
                            showFeedback('danger', 'Failed to load programs');
                        }
                    })
                    .catch(error => {
                        programSelect.disabled = false;
                        programSelect.innerHTML = '<option value="">Select Program</option>';
                        console.error('Error:', error);
                        showFeedback('danger', 'Failed to load programs');
                    });
            });

            // Form submission
            document.getElementById('leadForm').addEventListener('submit', function(e) {
                alert('submit');
                e.preventDefault();
                const submitBtn = document.getElementById('submitBtn');
                
                // Get recruiter ID from URL
                const urlParams = new URLSearchParams(window.location.search);
                const recruiterId = urlParams.get('rid');

                if (!recruiterId) {
                    showFeedback('danger', 'Invalid recruiter reference');
                    return;
                }

                // Validate phone number
                if (!phoneInput.isValidNumber()) {
                    showFeedback('danger', 'Please enter a valid phone number');
                    return;
                }

                // Validate required fields
                const requiredFields = ['fullName', 'email', 'phone', 'school', 'program'];
                let missingFields = requiredFields.filter(field => !document.getElementById(field).value);
                
                if (missingFields.length > 0) {
                    showFeedback('danger', 'Please fill in all required fields');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Submitting...
                `;

                // Prepare form data to match the API format
                const formData = {
                    name: document.getElementById('fullName').value,
                    email: document.getElementById('email').value,
                    contact: phoneInput.getNumber(), // This includes the country code
                    country: phoneInput.getSelectedCountryData().name, // Get country from phone input
                    program_id: document.getElementById('program').value,
                    lead_recruiter_id: recruiterId,
                    source_id: 1 // Set source to Website Form
                };

                // Debug: Log the form data
                console.log('Form data:', formData);
                console.log('API URL:', '../../api/leads/create_lead.php');

                // Submit form
                fetch('../../api/leads/create_lead.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    // Debug: Log the raw response
                    console.log('Raw response:', response);
                    return response.json();
                })
                .then(data => {
                    // Debug: Log the parsed data
                    console.log('Parsed data:', data);
                    
                    if (data.status) {
                            //show the feedback for 3 seconds then reload the page
                        setTimeout(() => {
                            showFeedback('success', `
                                <div class="text-center">
                                    <h4 class="mb-3">Thank you for your interest!</h4>
                                    <p>Your application has been received successfully.</p>
                                    <p>One of our recruiters will contact you soon to guide you through the next steps.</p>
                                </div>
                            `);
                            window.location.reload();
                        }, 3000);
                        document.getElementById('leadForm').reset();
                        
                        // Reset phone input
                        phoneInput.setCountry('ke');
                        
                        // Disable form fields temporarily
                        document.querySelectorAll('#leadForm input, #leadForm select, #leadForm button')
                            .forEach(element => element.disabled = true);
                        
                        // Re-enable form after 5 seconds
                        setTimeout(() => {
                            document.querySelectorAll('#leadForm input, #leadForm select, #leadForm button')
                                .forEach(element => element.disabled = false);
                        }, 5000);
                    } else {
                        showFeedback('danger', data.message || 'An error occurred. Please try again.');
                    }
                })
                .catch(error => {
                    // Debug: Log the error
                    console.error('Error details:', error);
                    showFeedback('danger', 'An error occurred. Please try again.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Application';
                });
            });

            // Enhanced feedback function with animation
            function showFeedback(type, message) {
                const feedback = document.getElementById('formFeedback');
                feedback.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                // Scroll to feedback
                feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    </script>
</body>
</html> 