-- Check if the recruiter exists and has correct role
SELECT u.*, ur.name as role_name
FROM users u
LEFT JOIN user_roles ur ON u.role_id = ur.id
WHERE u.email = 'john.doe@example.com';

-- Verify the password hash
SELECT password FROM users WHERE email = 'john.doe@example.com'; 