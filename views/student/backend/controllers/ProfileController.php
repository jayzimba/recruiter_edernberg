<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../utils/Helpers.php';
require_once __DIR__ . '/AuthController.php';

class ProfileController {
    private $studentModel;
    private $authController;

    public function __construct() {
        $this->studentModel = new Student();
        $this->authController = new AuthController();
        $this->authController->checkAuth();
    }

    public function getProfile() {
        $currentUser = $this->authController->getCurrentUser();
        $profile = $this->studentModel->getProfile($currentUser['id']);
        
        if ($profile) {
            Helpers::respondJson([
                'success' => true,
                'profile' => $profile
            ]);
        } else {
            Helpers::respondJson(['error' => 'Profile not found'], 404);
        }
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::respondJson(['error' => 'Invalid request method'], 405);
        }

        $currentUser = $this->authController->getCurrentUser();
        $allowedFields = ['phone', 'address', 'date_of_birth', 'nationality', 'emergency_contact'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $updateData[$field] = Helpers::sanitizeInput($_POST[$field]);
            }
        }

        if (empty($updateData)) {
            Helpers::respondJson(['error' => 'No valid fields to update'], 400);
        }

        // Handle profile image upload if present
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadDir = __DIR__ . '/../../uploads/avatars';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = Helpers::uploadFile(
                    $_FILES['avatar'],
                    $uploadDir,
                    ['jpg', 'jpeg', 'png']
                );

                $updateData['avatar'] = 'uploads/avatars/' . $fileName;
            } catch (RuntimeException $e) {
                Helpers::respondJson(['error' => $e->getMessage()], 400);
            }
        }

        if ($this->studentModel->updateProfile($currentUser['id'], $updateData)) {
            $updatedProfile = $this->studentModel->getProfile($currentUser['id']);
            Helpers::respondJson([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => $updatedProfile
            ]);
        } else {
            Helpers::respondJson(['error' => 'Failed to update profile'], 500);
        }
    }

    public function getCourses() {
        $currentUser = $this->authController->getCurrentUser();
        $courses = $this->studentModel->getCourses($currentUser['id']);
        
        Helpers::respondJson([
            'success' => true,
            'courses' => $courses
        ]);
    }

    public function getFinancialStatus() {
        $currentUser = $this->authController->getCurrentUser();
        $financialStatus = $this->studentModel->getFinancialStatus($currentUser['id']);
        
        Helpers::respondJson([
            'success' => true,
            'financial_status' => $financialStatus
        ]);
    }
}
?> 