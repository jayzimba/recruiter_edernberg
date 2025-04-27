-- Add subscriptions for all users from April 1st to May 1st, 2025
INSERT INTO subscriptions (recruiter_id, start_date, end_date, status, created_at, updated_at)
SELECT 
    u.id as recruiter_id,
    '2025-04-01' as start_date,
    '2025-05-01' as end_date,
    'active' as status,
    NOW() as created_at,
    NOW() as updated_at
FROM users u
WHERE u.id NOT IN (
    -- Exclude users who already have an active subscription during this period
    SELECT s.recruiter_id 
    FROM subscriptions s 
    WHERE s.status = 'active' 
    AND (
        (s.start_date <= '2025-04-01' AND s.end_date >= '2025-04-01')
        OR 
        (s.start_date <= '2025-05-01' AND s.end_date >= '2025-05-01')
        OR
        (s.start_date >= '2025-04-01' AND s.end_date <= '2025-05-01')
    )
); 