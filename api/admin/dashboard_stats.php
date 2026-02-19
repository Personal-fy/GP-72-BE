<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$stats = array();

// 1. Total Patients
$stmt = $db->query("SELECT COUNT(*) FROM patients");
$stats['total_patients'] = $stmt->fetchColumn();

// 2. Total Doctors
$stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'Doctor'");
$stats['total_doctors'] = $stmt->fetchColumn();

// 3. Appointments Today
$stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE DATE(schedule_date) = CURDATE()");
$stats['appointments_today'] = $stmt->fetchColumn();

// 4. Pending Lab Requests
$stmt = $db->query("SELECT COUNT(*) FROM lab_requests WHERE status = 'Requested'");
$stats['pending_lab_requests'] = $stmt->fetchColumn();

// 5. Low Stock Items
$stmt = $db->query("SELECT COUNT(*) FROM inventory WHERE quantity < reorder_level");
$stats['low_stock_items'] = $stmt->fetchColumn();

// 6. Recent Activities (Limit 5)
// Union of recent appointments and lab requests could be complex, 
// for now let's just show recent Logins (if we had a log) or just new patients.
$stmt = $db->query("SELECT name, created_at FROM patients ORDER BY created_at DESC LIMIT 5");
$stats['recent_patients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode($stats);
?>
