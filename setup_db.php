<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Hardcoded queries to avoid file parsing issues
    $queries = [
        "CREATE TABLE IF NOT EXISTS patients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            age INT NOT NULL,
            gender VARCHAR(10) NOT NULL,
            phone VARCHAR(15),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS inventory (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            quantity INT NOT NULL DEFAULT 0,
            reorder_level INT DEFAULT 10,
            unit_price DECIMAL(10, 2) NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('Doctor', 'Pharmacist', 'Receptionist', 'Lab_Tech', 'Admin') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        // Migration: Update role column if table exists but has old enum
        "ALTER TABLE users MODIFY COLUMN role ENUM('Doctor', 'Pharmacist', 'Receptionist', 'Lab_Tech', 'Admin') NOT NULL",
        
        "CREATE TABLE IF NOT EXISTS prescriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            inventory_id INT NOT NULL,
            dosage VARCHAR(100),
            status ENUM('Pending', 'Dispensed', 'Cancelled') DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (inventory_id) REFERENCES inventory(id)
        )",
        "CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            schedule_date DATETIME NOT NULL,
            status ENUM('Scheduled', 'Checked-In', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS lab_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            test_type VARCHAR(100) NOT NULL,
            results TEXT,
            status ENUM('Requested', 'In-Progress', 'Completed') DEFAULT 'Requested',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    $output = array();
    foreach($queries as $query) {
        try {
            $db->exec($query);
            $output[] = "Success: Table created/checked.";
        } catch (PDOException $e) {
            $output[] = "Error: " . $e->getMessage();
        }
    }

    echo json_encode(array("message" => "Setup execution finished.", "details" => $output));

} catch(PDOException $e) {
    http_response_code(503);
    echo json_encode(array("message" => "Error: " . $e->getMessage()));
}
?>
