<?php
require_once '../config/database.php';

// Fetch active programs
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT p.*, s.school_name, l.description as level_name 
          FROM programs p 
          LEFT JOIN schools s ON p.school_id = s.id
          LEFT JOIN levels l ON p.level_id = l.id
          ORDER BY p.program_name";
$stmt = $conn->query($query);
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study at Edernberg - Transform Your Future</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #6c757d;
        }

        .hero-section {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.6)),
                        url('../assets/image.png');
            background-size: cover;
            background-position: center;
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: left;
            max-width: 650px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            animation: fadeInUp 1s ease;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            animation: fadeInUp 1s ease 0.4s;
        }

        .hero-btn {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            z-index: 1;
        }

        .hero-btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
        }

        .hero-btn-primary:hover {
            background: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
            color: white;
        }

        .hero-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
        }

        .hero-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            color: white;
        }

        .hero-btn i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .hero-btn:hover i {
            transform: translateX(5px);
        }

        .hero-btn::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.2);
            transform: scale(0);
            transition: transform 0.5s ease;
            border-radius: 50px;
        }

        .hero-btn:active::after {
            transform: scale(2);
            opacity: 0;
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

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            .hero-btn {
                width: 100%;
                padding: 0.875rem 2rem;
            }
            .hero-content {
                text-align: center;
                padding: 0 1rem;
            }
        }

        .features-section {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .feature-card {
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .programs-section {
            padding: 5rem 0;
        }

        .program-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .program-card:hover {
            transform: translateY(-5px);
        }

        .program-image {
            height: 200px;
            object-fit: cover;
        }

        .apply-btn {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 30px;
            border: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .apply-btn:hover {
            background: #357abd;
            transform: translateY(-2px);
        }

        .stats-section {
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)),
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80');
            background-size: cover;
            background-position: center;
            padding: 5rem 0;
            color: white;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        footer {
            background: #1a237e;
            color: white;
            padding: 3rem 0;
        }

        /* Add these new navbar styles */
        .navbar {
            padding: 1rem 0;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand img {
            height: 65px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .nav-item {
            position: relative;
            margin: 0 0.25rem;
        }

        .nav-link {
            color: #2c3e50 !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            border-radius: 50px;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(74, 144, 226, 0.1);
        }

        .nav-link.active {
            color: var(--primary-color) !important;
            background: rgba(74, 144, 226, 0.1);
        }

        .navbar-cta {
            background: var(--primary-color);
            color: white !important;
            padding: 0.5rem 1.5rem !important;
            border-radius: 50px;
            margin-left: 1rem;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
        }

        .navbar-cta:hover {
            background: #357abd !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.3);
            color: white !important;
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            width: 24px;
            height: 24px;
            background-image: none !important;
            position: relative;
        }

        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 2px;
            background-color: #2c3e50;
            transition: all 0.3s ease;
        }

        .navbar-toggler-icon::before {
            top: 8px;
        }

        .navbar-toggler-icon::after {
            bottom: 8px;
        }

        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon::before {
            transform: rotate(45deg);
            top: 11px;
        }

        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon::after {
            transform: rotate(-45deg);
            bottom: 11px;
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                padding: 1rem;
                border-radius: 10px;
                margin-top: 1rem;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            }

            .navbar-cta {
                margin: 0.5rem 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../assets/logo.png" alt="Edernberg Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#programs">Programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Why Choose Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbar-cta" href="#apply">
                            <i class="bi bi-arrow-right-circle me-1"></i>Apply Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title text-white">Transform Your Future with Edernberg</h1>
                <p class="hero-subtitle text-white">Join our world-class programs and unlock your potential in a dynamic learning environment designed for tomorrow's leaders.</p>
                <div class="hero-buttons">
                    <a href="#apply" class="hero-btn hero-btn-primary">
                        Apply Now <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#programs" class="hero-btn hero-btn-secondary">
                        Explore Programs <i class="bi bi-grid"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Edernberg</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-trophy feature-icon"></i>
                        <h4>World-Class Education</h4>
                        <p>Experience learning from industry experts and acclaimed faculty</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-globe feature-icon"></i>
                        <h4>Global Recognition</h4>
                        <p>Our programs are internationally recognized and accredited</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-laptop feature-icon"></i>
                        <h4>Modern Facilities</h4>
                        <p>Access to state-of-the-art learning resources and facilities</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="programs-section">
        <div class="container">
            <h2 class="text-center mb-5">Our Programs</h2>
            <div class="row g-4">
                <?php foreach ($programs as $program): ?>
                <div class="col-md-4">
                    <div class="card program-card">
                        <img src="https://source.unsplash.com/random/400x300/?university,education&sig=<?php echo $program['id']; ?>" 
                             class="program-image" alt="Program Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($program['program_name']); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($program['school_name']); ?> | 
                                    <?php echo htmlspecialchars($program['level_name']); ?>
                                </small>
                            </p>
                            <p class="card-text">Duration: <?php echo htmlspecialchars($program['duration']); ?> months</p>
                            <button class="btn btn-primary" onclick="selectProgram(<?php echo $program['id']; ?>)">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">5000+</div>
                        <div>Students Enrolled</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div>Programs Offered</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">95%</div>
                        <div>Employment Rate</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">30+</div>
                        <div>Years of Excellence</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Form Section -->
    <section id="apply" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <h3 class="text-center mb-4">Apply Now</h3>
                            <form id="applicationForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="firstname" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="lastname" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="contact" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Program</label>
                                        <select class="form-select" name="program_id" required>
                                            <option value="">Select Program</option>
                                            <?php foreach ($programs as $program): ?>
                                            <option value="<?php echo $program['id']; ?>">
                                                <?php echo htmlspecialchars($program['program_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn apply-btn w-100">Submit Application</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p>
                        <i class="bi bi-geo-alt"></i> 123 Education Street<br>
                        <i class="bi bi-envelope"></i> admissions@edernberg.edu<br>
                        <i class="bi bi-telephone"></i> +1234567890
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Programs</a></li>
                        <li><a href="#" class="text-white">Apply Now</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch('../api/applications/create_public.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    alert('Application submitted successfully!');
                    this.reset();
                } else {
                    alert(data.message || 'Failed to submit application');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        function selectProgram(programId) {
            document.querySelector('select[name="program_id"]').value = programId;
            document.getElementById('apply').scrollIntoView({ behavior: 'smooth' });
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.padding = '0.5rem 0';
            } else {
                navbar.style.padding = '1rem 0';
            }
        });

        // Active link handling
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html> 