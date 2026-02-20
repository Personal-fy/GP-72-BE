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
include_once '../../models/User.php'; // Re-using User model

$database = new Database();
$db = $database->getConnection();

// Quick query to get all doctors
$query = "SELECT id, name FROM users WHERE role = 'Doctor'";
$stmt = $db->prepare($query);
$stmt->execute();

$doctors_arr = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $doctor_item = array(
        "id" => $id,
        "name" => $name
    );
    array_push($doctors_arr, $doctor_item);
}

http_response_code(200);
echo json_encode($doctors_arr);
?>
