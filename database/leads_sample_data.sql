-- Sample data for leads table
INSERT INTO leads (name, email, contact, country, program_id, contacted, converted, notes, source, lead_recruiter_id) VALUES
('John Smith', 'john.smith@email.com', '+254712345678', 'Kenya', 1, 1, 1, 'Very interested in Business Administration program. Has previous experience.', 'Facebook Ad', 1),
('Mary Johnson', 'mary.j@email.com', '+255798765432', 'Tanzania', 2, 1, 0, 'Called twice, waiting for documents.', 'Website Form', 1),
('David Omondi', 'david.o@email.com', '+254723456789', 'Kenya', 3, 0, 0, 'New lead from Facebook campaign.', 'Facebook Ad', 2),
('Sarah Mutua', 'sarah.m@email.com', '+254734567890', 'Kenya', 1, 1, 0, 'Scheduled follow-up for next week.', 'Referral', 2),
('James Okoro', 'james.o@email.com', '+234567890123', 'Nigeria', 4, 1, 1, 'Successfully converted to student.', 'Facebook Ad', 1),
('Grace Kwamboka', 'grace.k@email.com', '+254745678901', 'Kenya', 2, 0, 0, 'Interested in evening classes.', 'Website Form', 3),
('Peter Mensah', 'peter.m@email.com', '+233876543210', 'Ghana', 3, 1, 0, 'Following up on documentation requirements.', 'Other', 2),
('Lucy Akinyi', 'lucy.a@email.com', '+254756789012', 'Kenya', 1, 1, 0, 'Scheduled for campus visit.', 'Referral', 1),
('Mohammed Ali', 'mohammed.a@email.com', '+255887654321', 'Tanzania', 4, 0, 0, 'New inquiry about IT programs.', 'Facebook Ad', 3),
('Faith Wanjiru', 'faith.w@email.com', '+254767890123', 'Kenya', 2, 1, 1, 'Completed application process.', 'Website Form', 2);

-- Sample data for lead_activities
INSERT INTO lead_activities (lead_id, user_id, activity_type, description) VALUES
(1, 1, 'contact_attempt', 'Initial phone call made. Discussed program details.'),
(1, 1, 'status_changed', 'Marked as contacted after successful call.'),
(1, 1, 'converted', 'Application completed and submitted.'),
(2, 1, 'contact_attempt', 'First call attempt - no answer.'),
(2, 1, 'contact_attempt', 'Second call successful, discussed requirements.'),
(2, 1, 'note_added', 'Candidate will submit documents by end of week.'),
(3, 2, 'note_added', 'New lead from recent Facebook campaign.'),
(4, 2, 'contact_attempt', 'Discussed program details and fees.'),
(4, 2, 'status_changed', 'Marked as contacted after successful discussion.'),
(5, 1, 'contact_attempt', 'Initial consultation completed.'),
(5, 1, 'converted', 'Successfully enrolled in program.'),
(6, 3, 'note_added', 'Interested in flexible study options.'),
(7, 2, 'contact_attempt', 'Provided information about required documents.'),
(8, 1, 'contact_attempt', 'Scheduled campus visit for next Tuesday.'),
(9, 3, 'note_added', 'Inquired about IT program structure.'),
(10, 2, 'converted', 'Completed enrollment process.');

-- Sample data for lead_followups
INSERT INTO lead_followups (lead_id, user_id, followup_date, status, notes) VALUES
(2, 1, DATE_ADD(NOW(), INTERVAL 2 DAY), 'pending', 'Follow up on document submission.'),
(3, 2, DATE_ADD(NOW(), INTERVAL 1 DAY), 'pending', 'Initial contact attempt.'),
(4, 2, DATE_ADD(NOW(), INTERVAL 3 DAY), 'pending', 'Discuss financial options.'),
(6, 3, DATE_ADD(NOW(), INTERVAL 1 WEEK), 'pending', 'Follow up on program interest.'),
(7, 2, DATE_ADD(NOW(), INTERVAL 2 DAY), 'completed', 'Check document status.'),
(8, 1, DATE_ADD(NOW(), INTERVAL 4 DAY), 'pending', 'Campus visit follow-up.'),
(9, 3, DATE_ADD(NOW(), INTERVAL 2 DAY), 'pending', 'Provide detailed program information.'),
(2, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'completed', 'Second follow-up call completed.'),
(4, 2, DATE_SUB(NOW(), INTERVAL 2 DAY), 'completed', 'Initial consultation completed.'),
(7, 2, DATE_SUB(NOW(), INTERVAL 3 DAY), 'cancelled', 'Rescheduled for next week.');

-- Update some leads to show progression
UPDATE leads SET 
    contacted = 1, 
    updated_at = NOW() 
WHERE id IN (1, 2, 4, 5, 7, 8, 10);

UPDATE leads SET 
    converted = 1, 
    updated_at = NOW() 
WHERE id IN (1, 5, 10);

-- Update completed followups
UPDATE lead_followups SET 
    status = 'completed',
    completed_at = NOW(),
    updated_at = NOW()
WHERE status = 'completed';

-- Add some additional lead sources if needed
INSERT INTO lead_sources (name, description) VALUES
('Instagram Ad', 'Leads generated from Instagram advertising campaigns'),
('Education Fair', 'Leads collected during education fairs and exhibitions'),
('Agent Referral', 'Leads referred by educational agents'),
('Google Ads', 'Leads from Google advertising campaigns');

-- Verify data integrity
SELECT 'Leads Count:', COUNT(*) FROM leads;
SELECT 'Activities Count:', COUNT(*) FROM lead_activities;
SELECT 'Followups Count:', COUNT(*) FROM lead_followups;
SELECT 'Sources Count:', COUNT(*) FROM lead_sources; 