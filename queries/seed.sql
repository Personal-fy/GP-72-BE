-- Database Seed Script for HMS
-- Note: The password hash used here is a PLACEHOLDER.
-- You should generate a valid BCrypt hash for 'password123' using PHP:
-- php -r "echo password_hash('password123', PASSWORD_BCRYPT);"
-- And replace 'YOUR_HASH_HERE' with the output.
-- Example valid hash for 'password123': $2y$10$ReplaceMeWithRealHashGeneratedByPHP

-- 1. Users (All Roles)
INSERT INTO users (name, username, password, role) VALUES
('Dr. Sarah Smith', 'doctor', 'Ilobu@07', 'Doctor'),
('John Receptionist', 'reception', 'Ilobu@07', 'Receptionist'),
('Jane Pharmacist', 'pharmacy', 'Ilobu@07', 'Pharmacist'),
('Mike LabTech', 'lab', 'Ilobu@07', 'Lab_Tech'),
('Admin User', 'admin', 'Ilobu@07', 'Admin');

-- 2. Patients
INSERT INTO patients (name, age, gender, phone, address) VALUES
('Alice Johnson', 30, 'Female', '555-0101', '123 Maple St'),
('Bob Williams', 45, 'Male', '555-0102', '456 Oak Ave'),
('Charlie Brown', 10, 'Male', '555-0103', '789 Pine Ln'),
('Diana Prince', 28, 'Female', '555-0104', '321 Elm St');

-- 3. Inventory (Pharmacy)
INSERT INTO inventory (name, category, quantity, reorder_level, unit_price, updated_at) VALUES
('Paracetamol 500mg', 'Tablet', 500, 100, 0.50, NOW()),
('Amoxicillin 250mg', 'Capsule', 50, 50, 1.20, NOW()), -- Low Stock
('Ibuprofen 400mg', 'Tablet', 200, 50, 0.80, NOW()),
('Vitamin C', 'Supplement', 1000, 200, 0.30, NOW()),
('Cough Syrup', 'Liquid', 15, 20, 5.00, NOW()); -- Low Stock

-- 4. Appointments (Reception/Doctor)
-- Assuming IDs: Patient 1-4, Doctor 1 (Id might be different if auto-increment, adjust if needed)
-- We use subqueries to get IDs to be safer if tables aren't empty, but for seeding empty tables hardcoded IDs are usually fine or use INSERT INTO ... SELECT.
-- Here we imply 1-based IDs from the inserts above.

INSERT INTO appointments (patient_id, doctor_id, schedule_date, status, created_at) VALUES
(1, 1, DATE_ADD(NOW(), INTERVAL 2 HOUR), 'Scheduled', NOW()), -- Today
(2, 1, DATE_ADD(NOW(), INTERVAL 3 HOUR), 'In Progress', NOW()), -- Today
(3, 1, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Scheduled', NOW()),    -- Tomorrow
(4, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Completed', NOW());    -- Yesterday

-- 5. Lab Requests (Lab/Doctor)
INSERT INTO lab_requests (patient_id, doctor_id, test_type, status, created_at) VALUES
(1, 1, 'Blood Test', 'Requested', NOW()),
(2, 1, 'X-Ray', 'Completed', NOW()),
(3, 1, 'Urine Analysis', 'In Progress', NOW());

-- 6. Prescriptions (Doctor/Pharmacy)
-- Links to Inventory Items (1: Paracetamol, 2: Amoxicillin)
INSERT INTO prescriptions (patient_id, doctor_id, inventory_id, dosage, status, created_at) VALUES
(1, 1, 1, '2 tablets twice daily', 'Pending', NOW()),
(2, 1, 2, '1 capsule every 8 hours', 'Dispensed', NOW());
