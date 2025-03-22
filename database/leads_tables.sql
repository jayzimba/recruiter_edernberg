-- Create leads table
CREATE TABLE IF NOT EXISTS leads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contact VARCHAR(50),
    country VARCHAR(100),
    program_id INT,
    contacted TINYINT(1) DEFAULT 0,
    converted TINYINT(1) DEFAULT 0,
    notes TEXT,
    source VARCHAR(100) DEFAULT 'Facebook Ad',
    lead_recruiter_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id),
    FOREIGN KEY (lead_recruiter_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create lead_activities table to track all interactions with leads
CREATE TABLE IF NOT EXISTS lead_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    user_id INT NOT NULL,
    activity_type ENUM('contact_attempt', 'note_added', 'status_changed', 'converted') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create lead_sources table for tracking different lead sources
CREATE TABLE IF NOT EXISTS lead_sources (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default lead sources
INSERT INTO lead_sources (name, description) VALUES
('Facebook Ad', 'Leads generated from Facebook advertising campaigns'),
('Website Form', 'Leads from the website contact form'),
('Referral', 'Leads referred by existing students or partners'),
('Other', 'Leads from other sources');

-- Create lead_followups table for scheduling and tracking follow-ups
CREATE TABLE IF NOT EXISTS lead_followups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    user_id INT NOT NULL,
    followup_date DATETIME NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better query performance
ALTER TABLE leads ADD INDEX idx_email (email);
ALTER TABLE leads ADD INDEX idx_contact (contact);
ALTER TABLE leads ADD INDEX idx_country (country);
ALTER TABLE leads ADD INDEX idx_created_at (created_at);
ALTER TABLE leads ADD INDEX idx_contacted (contacted);
ALTER TABLE leads ADD INDEX idx_converted (converted);

-- Add index for lead activities
ALTER TABLE lead_activities ADD INDEX idx_created_at (created_at);
ALTER TABLE lead_activities ADD INDEX idx_activity_type (activity_type);

-- Add index for lead followups
ALTER TABLE lead_followups ADD INDEX idx_followup_date (followup_date);
ALTER TABLE lead_followups ADD INDEX idx_status (status); 