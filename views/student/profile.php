<?php
require_once 'includes/session_check.php';
$page = 'profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../assets/css/student-dashboard.css" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .profile-header {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.5;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            overflow: hidden;
            position: relative;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-avatar .avatar-placeholder {
            font-size: 4rem;
            color: #6c757d;
        }
        .profile-info {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }
        .info-group {
            margin-bottom: 1.5rem;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .info-value {
            font-size: 1.1rem;
            color: #212529;
        }
        .edit-profile-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .edit-profile-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .profile-container {
                padding: 1rem;
            }
            .profile-header {
                padding: 1.5rem;
            }
            .profile-avatar {
                width: 120px;
                height: 120px;
            }
            .profile-info {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="profile-container">
        <div class="profile-header text-center">
            <button class="edit-profile-btn">
                <i class="fas fa-edit me-2"></i>Edit Profile
            </button>
            
            <div class="profile-avatar">
                <?php if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile Image">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <h2 class="mb-1"><?php echo htmlspecialchars($_SESSION['student_name']); ?></h2>
            <p class="mb-0"><?php echo htmlspecialchars($_SESSION['program_name']); ?></p>
        </div>

        <div class="profile-info">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Student ID</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['student_id']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['phone'] ?? 'Not provided'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['dob'] ?? 'Not provided'); ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Nationality</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['nationality'] ?? 'Not provided'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['gender'] ?? 'Not provided'); ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="info-group">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['address'] ?? 'Not provided'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>