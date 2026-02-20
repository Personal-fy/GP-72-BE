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
    "totalRequests" => 0,
    "pendingRequests" => 0,
    "completedRequests" => 0,
    "urgentRequests" => 0
);

// 1. Total Requests
$query = "SELECT COUNT(*) as count FROM lab_requests";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['totalRequests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// 2. Pending Requests
$query = "SELECT COUNT(*) as count FROM lab_requests WHERE status = 'Pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['pendingRequests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// 3. Completed Requests
$query = "SELECT COUNT(*) as count FROM lab_requests WHERE status = 'Completed'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['completedRequests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// 4. Urgent Requests (assuming 'Urgent' priority exists, or just use Pending as placeholder if not)
// Checking if priority column exists or using a different metric.
// For now, let's count 'In Progress' as active/urgent or check for high priority if schema supports.
// Checking schema from previous knowledge: LabRequests has status, test_type, but priority might not be there.
// Let's use 'In Progress' count for now as a proxy for active work.
$query = "SELECT COUNT(*) as count FROM lab_requests WHERE status = 'In Progress'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['urgentRequests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

http_response_code(200);
echo json_encode($stats);
?>
