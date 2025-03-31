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
            <a class="nav-link <?php echo ($page == 'assignments') ? 'active' : ''; ?>" href="assignments.php">
                <i class="fas fa-bed"></i>
                <span>Accomodation</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'results') ? 'active' : ''; ?>" href="results.php">
                <i class="fas fa-chart-bar"></i>
                <span>Results</span>
            </a>
        </li>
       
       
    </ul>
</div> 