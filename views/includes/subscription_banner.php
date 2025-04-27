<?php
if (isset($_SESSION['user_id']) && isset($_SESSION['subscription_end_date'])) {
    $endDate = new DateTime($_SESSION['subscription_end_date']);
    $today = new DateTime();
    $daysUntilEnd = $today->diff($endDate)->days;
    
    // Only show banner if subscription is active and within 7 days of ending
    if ($_SESSION['subscription_status'] === 'active' && $daysUntilEnd <= 7) {
        $message = $daysUntilEnd === 0 
            ? "Your subscription ends today!" 
            : "Your subscription will end in {$daysUntilEnd} " . ($daysUntilEnd === 1 ? 'day' : 'days'). ": Contact Support for assistance +260963676321 ";
        
        $alertClass = $daysUntilEnd === 0 ? 'alert-danger' : 'alert-warning';
        ?>
        <div class="subscription-banner alert <?php echo $alertClass; ?> alert-dismissible fade show mb-0" role="alert">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <style>
            .subscription-banner {
                border-radius: 0;
                margin-bottom: 0 !important;
                position: sticky;
                top: 0;
                z-index: 1030;
            }
            .subscription-banner .container {
                max-width: 100%;
            }
        </style>
        <?php
    }
}
?> 