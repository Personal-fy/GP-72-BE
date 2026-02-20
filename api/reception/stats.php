<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$stats = array(
    "totalPatients" => 0,
    "totalDoctors" => 0,
    "todayAppointments" => 0,
    "waitingQueue" => 0
);

// 1. Total Patients
$query = "SELECT COUNT(*) as count FROM patients";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['totalPatients'] = $row['count'];

// 2. Total Doctors
$query = "SELECT COUNT(*) as count FROM users WHERE role = 'Doctor'";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['totalDoctors'] = $row['count'];

// 3. Today's Appointments
$query = "SELECT COUNT(*) as count FROM appointments WHERE DATE(schedule_date) = CURDATE()";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['todayAppointments'] = $row['count'];

// 4. Waiting Queue (Scheduled or In Progress today)
$query = "SELECT COUNT(*) as count FROM appointments WHERE DATE(schedule_date) = CURDATE() AND status IN ('Scheduled', 'In Progress')";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stats['waitingQueue'] = $row['count'];

http_response_code(200);
echo json_encode($stats);
?>
