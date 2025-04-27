<?php
require_once '../config/database.php';

class SubscriptionManager {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addSubscription($recruiterId, $startDate, $endDate) {
        try {
            $query = "INSERT INTO subscriptions (recruiter_id, start_date, end_date, status) 
                     VALUES (:recruiter_id, :start_date, :end_date, 'active')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':recruiter_id', $recruiterId);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Subscription error: " . $e->getMessage());
            return false;
        }
    }

    public function getRecruiters() {
        $query = "SELECT u.id, u.firstname, u.lastname, u.email 
                 FROM users u 
                 JOIN user_roles ur ON u.role_id = ur.id 
                 WHERE ur.name IN ('lead_recruiter', 'recruiter')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$manager = new SubscriptionManager();
$recruiters = $manager->getRecruiters();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recruiterId = $_POST['recruiter_id'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    
    if ($recruiterId && $startDate && $endDate) {
        $success = $manager->addSubscription($recruiterId, $startDate, $endDate);
        $message = $success ? "Subscription added successfully!" : "Failed to add subscription.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .subscription-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .btn-primary {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="subscription-form">
        <h2 class="form-title">Add New Subscription</h2>
        
        <?php if (isset($message)): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="recruiter_id" class="form-label">Select Recruiter</label>
                <select class="form-select" id="recruiter_id" name="recruiter_id" required>
                    <option value="">Choose a recruiter...</option>
                    <?php foreach ($recruiters as $recruiter): ?>
                        <option value="<?php echo $recruiter['id']; ?>">
                            <?php echo htmlspecialchars($recruiter['firstname'] . ' ' . $recruiter['lastname'] . ' (' . $recruiter['email'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-2"></i>Add Subscription
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set end date to 30 days after start date
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + 30);
            
            // Format the date as YYYY-MM-DD
            const formattedEndDate = endDate.toISOString().split('T')[0];
            document.getElementById('end_date').value = formattedEndDate;
        });
    </script>
</body>
</html> 