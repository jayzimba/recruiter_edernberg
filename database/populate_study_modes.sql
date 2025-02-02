INSERT INTO study_modes (mode_name) VALUES 
('Full Time'),
('Part Time'),
('Distance Learning'),
('Block Release'),
('Evening Classes'),
('Weekend Classes'),
('Online Learning')
ON DUPLICATE KEY UPDATE mode_name = VALUES(mode_name); 