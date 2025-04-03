<!-- Sidebar -->
<div class="sidebar">
    <div class="user-info">
        <div class="user-image">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-details">
            <h6><?php echo $_SESSION['student_name']; ?></h6>
            <small><?php echo $_SESSION['program_name']; ?></small>
        </div>
    </div>

    <div class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Add LMS Link -->
            <li class="nav-item">
                <a class="nav-link" href="https://lms.edernberg.ac.ke" target="_blank">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Learning Portal (LMS)</span>
                    <i class="fas fa-external-link-alt ms-auto external-link"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'courses') ? 'active' : ''; ?>" href="courses.php">
                    <i class="fas fa-book"></i>
                    <span>My Courses</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'fees') ? 'active' : ''; ?>" href="fees.php">
                    <i class="fas fa-money-bill"></i>
                    <span>Finances</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'accommodation') ? 'active' : ''; ?>" href="accommodation.php">
                    <i class="fas fa-bed"></i>
                    <span>Accomodation</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'transcripts') ? 'active' : ''; ?>" href="transcripts.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Transcripts</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Bottom Section with User Options -->
    <div class="sidebar-bottom mt-auto">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>

        <a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close btn-close-white modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-cover"></div>
                        <div class="profile-avatar-wrapper">
                            <?php if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile Image" class="profile-avatar">
                            <?php else: ?>
                                <div class="profile-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-title">
                            <h2><?php echo htmlspecialchars($_SESSION['student_name']); ?></h2>
                            <p class="program-name"><?php echo htmlspecialchars($_SESSION['program_name']); ?></p>
                            <div class="student-id">
                                <span class="badge">Student ID: <?php echo htmlspecialchars($_SESSION['student_id']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="info-grid">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <label>Email</label>
                                    <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="info-content">
                                    <label>Phone</label>
                                    <span><?php echo htmlspecialchars($_SESSION['phone'] ?? 'Not provided'); ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <label>Date of Birth</label>
                                    <span><?php echo htmlspecialchars($_SESSION['dob'] ?? 'Not provided'); ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="info-content">
                                    <label>Nationality</label>
                                    <span><?php echo htmlspecialchars($_SESSION['nationality'] ?? 'Not provided'); ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-venus-mars"></i>
                                </div>
                                <div class="info-content">
                                    <label>Gender</label>
                                    <span><?php echo htmlspecialchars($_SESSION['gender'] ?? 'Not provided'); ?></span>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <label>Address</label>
                                    <span><?php echo htmlspecialchars($_SESSION['address'] ?? 'Not provided'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar {
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 250px;
    background: #fff;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    z-index: 1000;
    padding-top: 60px; /* Add padding to account for header height */
}

.user-info {
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
}

.user-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6c757d;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-details h6 {
    margin: 0;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-details small {
    display: block;
    color: #6c757d;
    font-size: 0.8rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 0;
    margin-bottom: auto;
}

.sidebar-bottom {
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    position: sticky;
    bottom: 0;
    z-index: 1;
    margin-top: auto;
}

.sidebar-bottom .nav-link {
    padding: 0.5rem 1rem;
    color: #333;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-radius: 4px;
}

.sidebar-bottom .nav-link:hover {
    background: rgba(0, 0, 0, 0.05);
}

.sidebar-bottom .nav-link.text-danger:hover {
    background: rgba(220, 53, 69, 0.1);
}

.sidebar-bottom .nav-link i {
    width: 20px;
    text-align: center;
}

/* Profile Modal Styles */
.profile-container {
    position: relative;
}

.profile-header {
    position: relative;
    text-align: center;
    padding-bottom: 2rem;
}

.profile-cover {
    height: 150px;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.85), rgba(0, 123, 255, 0.85)), url('../../assets/icons/bg.png');
    background-size: cover;
    background-position: center;
    border-radius: 10px 10px 0 0;
    margin: -1rem -1rem 0;
    position: relative;
    overflow: hidden;
}

.profile-cover::before {
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

.profile-avatar-wrapper {
    position: relative;
    margin-top: -50px;
    display: inline-block;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid #fff;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #6c757d;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.profile-title {
    margin-top: 1rem;
}

.profile-title h2 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
    color: #2c3e50;
}

.program-name {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.student-id .badge {
    background: #e3f2fd;
    color: #0d6efd;
    font-weight: 500;
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.profile-info {
    padding: 2rem 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.info-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-card:hover {
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transform: translateY(-2px);
}

.info-icon {
    width: 40px;
    height: 40px;
    background: #e3f2fd;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d6efd;
    font-size: 1.2rem;
}

.info-content {
    flex: 1;
}

.info-content label {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-content span {
    display: block;
    color: #2c3e50;
    font-size: 0.95rem;
    word-break: break-word;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .profile-cover {
        height: 120px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }

    .profile-title h2 {
        font-size: 1.25rem;
    }
}

/* Add this to your existing styles */
.modal-content {
    border: none;
    overflow: hidden;
}

.modal-close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.modal-close-btn:hover {
    opacity: 1;
}
</style>