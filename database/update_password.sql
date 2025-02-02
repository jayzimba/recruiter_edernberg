-- Update the password for john.doe@example.com with a fresh hash
UPDATE users 
SET password = (
    SELECT password FROM (
        SELECT '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' as password
    ) as temp
)
WHERE email = 'john.doe@example.com'; 