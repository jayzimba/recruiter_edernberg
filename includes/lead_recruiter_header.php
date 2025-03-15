<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
.header {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    position: sticky;
    top: 0;
    z-index: 0;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.menu-toggle {
    background: none;
    border: none;
    color: var(--primary-color);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}

.menu-toggle:hover {
    color: var(--primary-color-dark);
}

.dashboard-title {
    margin: 0;
    margin-left: 0.5rem;
}

@media (max-width: 768px) {
    .dashboard-title {
        display: none;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }

    .user-profile {
        gap: 8px;
    }

    .user-info {
        font-size: 0.875rem;
    }

    .user-info small {
        font-size: 0.75rem;
    }

    .menu-toggle {
        padding: 0.25rem;
    }
}

@media (min-width: 769px) {
    .menu-toggle {
        display: none;
    }
}
</style>

<!-- Header -->
<div class="header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="menu-toggle" id="menuToggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h4 class="dashboard-title">Lead Dashboard Overview</h4>
        </div>
        <div class="user-profile">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['user_email'], 0, 1)); ?>
            </div>
            <div class="user-info">
                <small class="text-muted">Welcome,</small>
                <div class="fw-bold"><?php echo $_SESSION['user_email']; ?></div>
            </div>
        </div>
    </div>
</div> 