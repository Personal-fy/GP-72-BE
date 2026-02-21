<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../../config/database.php';
include_once '../../models/Appointment.php';

$database = new Database();
$db = $database->getConnection();
$appointment = new Appointment($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->id) &&
    !empty($data->status)
){
    $appointment->id = $data->id;
    $appointment->status = $data->status;

    if($appointment->updateStatus()){
        http_response_code(200);
        echo json_encode(array("message" => "Appointment status was updated."));
    } else{
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update appointment status."));
    }
} else{
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update appointment status. Data is incomplete."));
}
?>
