-- ==============================================
-- BILLING TABLE - Run this on MySQL Workbench
-- Database: hms_group72 | Port: 5222
-- ==============================================

CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    appointment_id INT,
    consultation_fee DECIMAL(10, 2) NOT NULL DEFAULT 5000.00,
    pharmaceutical_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    lab_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('Unpaid', 'Paid', 'Partial') DEFAULT 'Unpaid',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
);
