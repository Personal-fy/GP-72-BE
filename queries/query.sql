CREATE DATABASE hms_group72;

USE hms_group72;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Drugs/Items
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL, -- e.g., 'Antibiotic', 'Analgesic'
    quantity INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,   -- The "Low Stock" threshold
    unit_price DECIMAL(10, 2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for Prescriptions (Links Patient -> Doctor -> Drug)
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,         -- ID of the logged-in doctor
    inventory_id INT NOT NULL,      -- The specific drug ID
    dosage VARCHAR(100),            -- e.g., "2 tablets x 3 times daily"
    status ENUM('Pending', 'Dispensed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id)
);

-- Table for Users (Doctors, Pharmacists, Receptionists, Lab Techs, Admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Doctor', 'Pharmacist', 'Receptionist', 'Lab_Tech', 'Admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Appointments (Module B)
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    schedule_date DATETIME NOT NULL,
    status ENUM('Scheduled', 'Checked-In', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- FOREIGN KEY (patient_id) REFERENCES patients(id), -- Optional: Add FKs if strict
    -- FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Table for Lab Requests (Module C/E)
CREATE TABLE IF NOT EXISTS lab_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT, -- Optional link to appointment
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL, -- Who requested it
    test_type VARCHAR(100) NOT NULL, -- e.g. "Malaria", "Widal"
    results TEXT, -- JSON or text results
    status ENUM('Requested', 'In-Progress', 'Completed') DEFAULT 'Requested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);