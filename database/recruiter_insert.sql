-- First make sure we have the correct roles
INSERT INTO user_roles (name) VALUES 
('Admin'),
('recruiter'),
('lead_recruiter')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Then insert the recruiter
INSERT INTO users (
    firstname,
    lastname,
    nrc_number,
    email,
    phone_number,
    password,
    role_id
) VALUES (
    'John',
    'Doe',
    'NRC123456/78/9',
    'john.doe@example.com',
    '+260123456789',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hashed version of Password@2025
    (SELECT id FROM user_roles WHERE name = 'recruiter')
); 