<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

// 2b. Total Users (All roles)
$stmt = $db->query("SELECT COUNT(*) FROM users");
$stats['total_users'] = $stmt->fetchColumn();

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
// 6. Recent Activities
$stmt = $db->query("SELECT name, created_at FROM patients ORDER BY created_at DESC LIMIT 5");
$stats['recent_patients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7. Total Appointments (all-time)
$stmt = $db->query("SELECT COUNT(*) FROM appointments");
$stats['appointments'] = $stmt->fetchColumn();

// 8. Total Revenue (estimated from dispensed prescriptions × unit_price)
$stmt = $db->query("SELECT COALESCE(SUM(i.unit_price), 0) FROM prescriptions p LEFT JOIN inventory i ON p.inventory_id = i.id WHERE p.status = 'Dispensed'");
$stats['revenue'] = floatval($stmt->fetchColumn());

// 9. Active Users (staff count as proxy)
$stats['active_users'] = $stats['total_users'];

http_response_code(200);
echo json_encode($stats);
?>
