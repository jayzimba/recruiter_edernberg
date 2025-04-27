<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Mailer.php';

class SubscriptionChecker {
    private $conn;
    private $mailer;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->mailer = new Mailer();
    }

    public function checkSubscriptions() {
        try {
            // Get all active subscriptions
            $query = "SELECT s.*, u.email, u.firstname, u.lastname 
                     FROM subscriptions s 
                     JOIN users u ON s.recruiter_id = u.id 
                     WHERE s.status = 'active'";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($subscriptions as $subscription) {
                $endDate = new DateTime($subscription['end_date']);
                $today = new DateTime();
                $lastDayOfMonth = new DateTime($endDate->format('Y-m-t'));
                $daysUntilEnd = $today->diff($endDate)->days;
                $daysUntilMonthEnd = $today->diff($lastDayOfMonth)->days;

                // If subscription has expired
                if ($endDate < $today) {
                    $this->updateSubscriptionStatus($subscription['id'], 'expired');
                    continue;
                }

                // If in last week of month
                if ($daysUntilMonthEnd <= 7 && $daysUntilEnd <= 7) {
                    $this->sendSubscriptionWarning($subscription, $daysUntilEnd);
                }
            }

            return true;
        } catch (PDOException $e) {
            error_log("Subscription check error: " . $e->getMessage());
            return false;
        }
    }

    private function updateSubscriptionStatus($subscriptionId, $status) {
        $query = "UPDATE subscriptions SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $subscriptionId);
        return $stmt->execute();
    }

    private function sendSubscriptionWarning($subscription, $daysRemaining) {
        $subject = "Subscription Renewal Reminder";
        $message = "
            <h2>Subscription Renewal Reminder</h2>
            <p>Dear {$subscription['firstname']} {$subscription['lastname']},</p>
            <p>Your subscription will expire in {$daysRemaining} days. Please renew to avoid service interruption.</p>
            <p>Subscription Details:</p>
            <ul>
                <li>End Date: " . date('F d, Y', strtotime($subscription['end_date'])) . "</li>
                <li>Days Remaining: {$daysRemaining}</li>
            </ul>
            <p>To renew your subscription, please contact the administrator or visit the subscription management page.</p>
            <p>Best regards,<br>Recruiter System</p>
        ";
        
        return $this->mailer->sendEmail($subscription['email'], $subject, $message);
    }
}

// Run the check
$checker = new SubscriptionChecker();
$checker->checkSubscriptions(); 