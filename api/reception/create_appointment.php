<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';
include_once '../../models/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$appointment = new Appointment($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->patient_id) &&
    !empty($data->doctor_id) &&
    !empty($data->schedule_date)
) {
    $appointment->patient_id = $data->patient_id;
    $appointment->doctor_id = $data->doctor_id;
    $appointment->schedule_date = $data->schedule_date;
    $appointment->status = 'Scheduled'; // Default status

    if($appointment->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Appointment was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create appointment."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create appointment. Data is incomplete."));
}
?>
