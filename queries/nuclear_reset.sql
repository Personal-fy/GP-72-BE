-- NUCLEAR RESET SCRIPT
-- WARNING: This will DELETE ALL TABLES and RECREATE them from scratch.
-- Use this if TRUNCATE is failing or if you want a completely fresh database state.

SET FOREIGN_KEY_CHECKS = 0;

-- 1. DROP EXISTING TABLES
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS lab_requests;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS inventory;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

-- 2. RECREATE TABLES (Schema from query.sql)
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,
    unit_price DECIMAL(10, 2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Doctor', 'Pharmacist', 'Receptionist', 'Lab_Tech', 'Admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    schedule_date DATETIME NOT NULL,
    status ENUM('Scheduled', 'Checked-In', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- Foreign keys omitted for simplicity as per original schema, but data integrity is maintained by logic
);

CREATE TABLE IF NOT EXISTS lab_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    test_type VARCHAR(100) NOT NULL,
    results TEXT,
    status ENUM('Requested', 'In-Progress', 'Completed') DEFAULT 'Requested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    inventory_id INT NOT NULL,
    dosage VARCHAR(100),
    status ENUM('Pending', 'Dispensed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id)
);

SET FOREIGN_KEY_CHECKS = 1;

-- 3. INSERT MASSIVE SEED DATA
-- Users
INSERT INTO users (name, username, password, role) VALUES
('Dr. Gregory House', 'house', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Doctor'),
('Dr. James Wilson', 'wilson', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Doctor'),
('Dr. Lisa Cuddy', 'cuddy', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Admin'),
('Dr. Eric Foreman', 'foreman', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Doctor'),
('Dr. Robert Chase', 'chase', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Doctor'),
('Dr. Allison Cameron', 'cameron', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Doctor'),
('Nurse Joy', 'reception', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Receptionist'),
('Pam Beesly', 'pam', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Receptionist'),
('Pharmacy Manager', 'pharmacy', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Pharmacist'),
('Lab Tech Mike', 'lab', '$2y$10$P8rtnQrbCd6BP0QLF8PZ4XEM7OgJBreHrWEyEwKF', 'Lab_Tech');

-- Patients
INSERT INTO patients (name, age, gender, phone, address, created_at) VALUES
('Alice Johnson', 34, 'Female', '555-0101', '123 Maple St', NOW()),
('Bob Williams', 45, 'Male', '555-0102', '456 Oak Ave', NOW()),
('Charlie Brown', 12, 'Male', '555-0103', '789 Pine Ln', NOW()),
('Diana Prince', 28, 'Female', '555-0104', '321 Elm St', NOW()),
('Evan Wright', 55, 'Male', '555-0105', '654 Birch Rd', NOW()),
('Fiona Gallagher', 23, 'Female', '555-0106', '987 Cedar Blvd', NOW()),
('George Costanza', 40, 'Male', '555-0107', '123 Monk St', NOW()),
('Hannah Abbott', 19, 'Female', '555-0108', 'Hogwarts, Dungeon 3', NOW()),
('Ian Malcolm', 38, 'Male', '555-0109', 'Jurassic Park', NOW()),
('Jack Sparrow', 35, 'Male', '555-0110', 'The Black Pearl', NOW()),
('Katherine Pierce', 145, 'Female', '555-0111', 'Mystic Falls', NOW()),
('Liam Neeson', 60, 'Male', '555-0112', 'Unknown Location', NOW()),
('Michael Scott', 45, 'Male', '555-0113', 'Scranton, PA', NOW()),
('Nancy Wheeler', 18, 'Female', '555-0114', 'Hawkins, IN', NOW()),
('Oscar Martinez', 35, 'Male', '555-0115', 'Scranton, PA', NOW()),
('Peter Parker', 17, 'Male', '555-0116', 'Queens, NY', NOW()),
('Quentin Tarantino', 50, 'Male', '555-0117', 'Hollywood, CA', NOW()),
('Rachel Green', 30, 'Female', '555-0118', 'New York, NY', NOW()),
('Steve Rogers', 100, 'Male', '555-0119', 'Brooklyn, NY', NOW()),
('Tony Stark', 45, 'Male', '555-0120', 'Malibu, CA', NOW()),
('Ursula Buffay', 32, 'Female', '555-0121', 'New York, NY', NOW()),
('Victor Frankenstein', 30, 'Male', '555-0122', 'Geneva, Switzerland', NOW()),
('Walter White', 50, 'Male', '555-0123', 'Albuquerque, NM', NOW()),
('Xena Warrior', 28, 'Female', '555-0124', 'Amphipolis', NOW()),
('Yoda', 900, 'Male', '555-0125', 'Dagobah', NOW()),
('Zack Morris', 18, 'Male', '555-0126', 'Bayside High', NOW()),
('Harry Potter', 17, 'Male', '555-0127', '4 Privet Drive', NOW()),
('Hermione Granger', 18, 'Female', '555-0128', 'Hampstead Garden', NOW()),
('Ron Weasley', 18, 'Male', '555-0129', 'The Burrow', NOW()),
('Draco Malfoy', 18, 'Male', '555-0130', 'Malfoy Manor', NOW());

-- Inventory
INSERT INTO inventory (name, category, quantity, reorder_level, unit_price, updated_at) VALUES
('Paracetamol 500mg', 'Tablet', 500, 100, 0.50, NOW()),
('Amoxicillin 250mg', 'Capsule', 45, 50, 1.20, NOW()),
('Ibuprofen 400mg', 'Tablet', 250, 50, 0.80, NOW()),
('Vitamin C 1000mg', 'Supplement', 1000, 200, 0.30, NOW()),
('Cough Syrup', 'Liquid', 10, 20, 5.00, NOW()),
('Bandages', 'Supply', 150, 30, 0.20, NOW()),
('Metformin 500mg', 'Tablet', 300, 100, 0.40, NOW()),
('Atorvastatin 10mg', 'Tablet', 200, 50, 1.50, NOW()),
('Omeprazole 20mg', 'Capsule', 400, 100, 1.10, NOW()),
('Amlodipine 5mg', 'Tablet', 0, 50, 0.60, NOW()),
('Cetirizine 10mg', 'Tablet', 600, 100, 0.25, NOW()),
('Aspirin 81mg', 'Tablet', 800, 100, 0.15, NOW()),
('Insulin Glargine', 'Injection', 20, 10, 25.00, NOW()),
('Azithromycin 250mg', 'Tablet', 60, 20, 3.00, NOW()),
('Ventolin Inhaler', 'Inhaler', 5, 10, 12.00, NOW()),
('Prednisone 5mg', 'Tablet', 100, 50, 0.35, NOW()),
('Gabapentin 300mg', 'Capsule', 150, 50, 0.90, NOW()),
('Tramadol 50mg', 'Capsule', 80, 20, 1.80, NOW()),
('Lorazepam 1mg', 'Tablet', 40, 20, 2.00, NOW()),
('Sertraline 50mg', 'Tablet', 220, 50, 1.10, NOW()),
('Furosemide 40mg', 'Tablet', 180, 50, 0.45, NOW()),
('Hydrochlorothiazide', 'Tablet', 300, 100, 0.30, NOW()),
('Pantoprazole 40mg', 'Tablet', 400, 100, 1.25, NOW()),
('Clopidogrel 75mg', 'Tablet', 150, 50, 2.50, NOW()),
('Montelukast 10mg', 'Tablet', 200, 50, 1.40, NOW());

-- Appointments
INSERT INTO appointments (patient_id, doctor_id, schedule_date, status, created_at) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL '09:00' HOUR_MINUTE), 'Checked-In', NOW()),
(2, 1, DATE_ADD(CURDATE(), INTERVAL '09:30' HOUR_MINUTE), 'In Progress', NOW()),
(3, 1, DATE_ADD(CURDATE(), INTERVAL '10:00' HOUR_MINUTE), 'Scheduled', NOW()),
(4, 2, DATE_ADD(CURDATE(), INTERVAL '10:30' HOUR_MINUTE), 'Scheduled', NOW()),
(5, 2, DATE_ADD(CURDATE(), INTERVAL '11:00' HOUR_MINUTE), 'Scheduled', NOW()),
(6, 4, DATE_ADD(CURDATE(), INTERVAL '11:30' HOUR_MINUTE), 'Scheduled', NOW()),
(7, 5, DATE_ADD(CURDATE(), INTERVAL '12:00' HOUR_MINUTE), 'Scheduled', NOW()),
(8, 1, DATE_ADD(CURDATE(), INTERVAL '13:00' HOUR_MINUTE), 'Scheduled', NOW()),
(9, 2, DATE_ADD(CURDATE(), INTERVAL '13:30' HOUR_MINUTE), 'Scheduled', NOW()),
(10, 4, DATE_ADD(CURDATE(), INTERVAL '14:00' HOUR_MINUTE), 'Scheduled', NOW()),
(11, 5, DATE_ADD(CURDATE(), INTERVAL '14:30' HOUR_MINUTE), 'Scheduled', NOW()),
(12, 1, DATE_ADD(CURDATE(), INTERVAL '15:00' HOUR_MINUTE), 'Scheduled', NOW()),
(13, 2, DATE_ADD(CURDATE(), INTERVAL '15:30' HOUR_MINUTE), 'Scheduled', NOW()),
(14, 4, DATE_ADD(CURDATE(), INTERVAL '16:00' HOUR_MINUTE), 'Scheduled', NOW()),
(15, 5, DATE_ADD(CURDATE(), INTERVAL '16:30' HOUR_MINUTE), 'Scheduled', NOW());

INSERT INTO appointments (patient_id, doctor_id, schedule_date, status, created_at) VALUES
(16, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Completed', NOW()),
(17, 2, DATE_SUB(NOW(), INTERVAL 1 DAY), 'Completed', NOW()),
(18, 4, DATE_SUB(NOW(), INTERVAL 2 DAY), 'Completed', NOW()),
(19, 5, DATE_SUB(NOW(), INTERVAL 2 DAY), 'Cancelled', NOW()),
(20, 1, DATE_SUB(NOW(), INTERVAL 3 DAY), 'Completed', NOW()),
(21, 1, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Scheduled', NOW()),
(22, 2, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Scheduled', NOW()),
(23, 4, DATE_ADD(NOW(), INTERVAL 2 DAY), 'Scheduled', NOW()),
(24, 5, DATE_ADD(NOW(), INTERVAL 3 DAY), 'Scheduled', NOW());

-- Lab Requests
INSERT INTO lab_requests (patient_id, doctor_id, test_type, status, created_at) VALUES
(1, 1, 'Full Blood Count', 'Requested', NOW()),
(2, 1, 'X-Ray Chest', 'In Progress', NOW()),
(3, 1, 'MRI Scan', 'Completed', NOW()),
(4, 2, 'Lipid Profile', 'Requested', NOW()),
(5, 2, 'Liver Function Test', 'Completed', NOW()),
(6, 4, 'Thyroid Panel', 'Requested', NOW()),
(7, 4, 'Urinalysis', 'In Progress', NOW()),
(8, 5, 'Blood Sugar Fasting', 'Completed', NOW());

-- Prescriptions
INSERT INTO prescriptions (patient_id, doctor_id, inventory_id, dosage, status, created_at) VALUES
(16, 1, 1, '1000mg every 6 hours', 'Dispensed', NOW()),
(17, 2, 3, '400mg after food', 'Dispensed', NOW()),
(18, 4, 7, '500mg twice daily with meals', 'Dispensed', NOW()),
(20, 1, 8, '10mg once daily at night', 'Dispensed', NOW()),
(1, 1, 2, '250mg three times daily', 'Pending', NOW()),
(2, 1, 5, '10ml every 4 hours', 'Pending', NOW()),
(3, 1, 4, '1 tablet daily', 'Pending', NOW()),
(4, 2, 6, 'Apply to affected area', 'Pending', NOW());
