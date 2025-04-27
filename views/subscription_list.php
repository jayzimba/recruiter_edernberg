<?php
require_once '../config/database.php';

class SubscriptionManager {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getSubscriptions() {
        $query = "SELECT s.*, u.firstname, u.lastname, u.email 
                 FROM subscriptions s 
                 JOIN users u ON s.recruiter_id = u.id 
                 ORDER BY s.end_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSubscriptionStatus($id, $status) {
        $query = "UPDATE subscriptions SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

$manager = new SubscriptionManager();
$subscriptions = $manager->getSubscriptions();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = $_POST['subscription_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if ($id && $status) {
        $success = $manager->updateSubscriptionStatus($id, $status);
        $message = $success ? "Subscription updated successfully!" : "Failed to update subscription.";
        header("Location: subscription_list.php?message=" . urlencode($message) . "&success=" . ($success ? '1' : '0'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .subscription-list {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
        }
        .table {
            margin-top: 1rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="subscription-list">
        <h2 class="page-title">Subscription Management</h2>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert <?php echo $_GET['success'] ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="subscription_management.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Subscription
            </a>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Recruiter</th>
                    <th>Email</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscriptions as $subscription): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subscription['firstname'] . ' ' . $subscription['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($subscription['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($subscription['start_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($subscription['end_date'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $subscription['status']; ?>">
                                <?php echo ucfirst($subscription['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $subscription['status'] === 'active' ? 'expired' : 'active'; ?>">
                                <button type="submit" name="action" value="toggle_status" class="btn btn-sm <?php echo $subscription['status'] === 'active' ? 'btn-danger' : 'btn-success'; ?>">
                                    <?php echo $subscription['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 