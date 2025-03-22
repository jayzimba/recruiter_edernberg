<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class Auth
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($email, $password)
    {
        try {
            $query = "SELECT u.*, ur.name as role_name 
                     FROM " . $this->table . " u 
                     LEFT JOIN user_roles ur ON u.role_id = ur.id 
                     WHERE u.email = :email LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return [
                    'status' => false,
                    'message' => 'User not found'
                ];
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Hash the provided password with SHA-256
            $hashed_password = hash('sha256', $password);

            // Compare the hashed passwords directly
            if ($hashed_password === $row['password']) {


                if ($row['status'] !== 1) {
                    return [
                        'status' => false,
                        'message' => 'User account is inactive'
                    ];
                }
                // Create session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_role'] = $row['role_name'];
                $_SESSION['user_email'] = $row['email'];
    

                // Create JWT token
                $token = $this->generateJWT($row);

                return [
                    'status' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => [
                        'id' => $row['id'],
                        'email' => $row['email'],
                        'role' => $row['role_name'],
                        'firstname' => $row['firstname'],
                        'lastname' => $row['lastname']
                    ]
                ];
            }

            return [
                'status' => false,
                'message' => 'Invalid credentials',
                'debug' => [
                    'provided_email' => $email,
                    'stored_hash' => $row['password'],
                    'provided_hash' => $hashed_password,
                    'role_name' => $row['role_name']
                ]
            ];
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        try {
            $database = new Database();
            $conn = $database->getConnection();

            // Verify current password
            $query = "SELECT password FROM users WHERE id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Hash the current password with SHA-256 for comparison
            $hashedCurrentPassword = hash('sha256', $currentPassword);
            if (!$user || $hashedCurrentPassword !== $user['password']) {
                throw new Exception('Current password is incorrect');
            }

            // Update password
            $newPasswordHash = hash('sha256', $newPassword);
            $query = "UPDATE users SET password = :password WHERE id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':password', $newPasswordHash);
            $stmt->bindParam(':user_id', $userId);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update password');
            }

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function generateJWT($user)
    {
        $secret_key = "your_secret_key_here";
        $issued_at = time();
        $expiration = $issued_at + (60 * 60); // Token valid for 1 hour

        $payload = [
            'iat' => $issued_at,
            'exp' => $expiration,
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role_name']
        ];

        return $this->encodeJWT($payload, $secret_key);
    }

    private function encodeJWT($payload, $secret_key)
    {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    public function logout()
    {
        // Clear all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        return [
            'status' => true,
            'message' => 'Logged out successfully'
        ];
    }

    public function getUserId()
    {
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        return null;
    }
}
