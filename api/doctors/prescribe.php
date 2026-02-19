<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Prescription.php';

$database = new Database();
$db = $database->getConnection();

$prescription = new Prescription($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->patient_id) &&
    !empty($data->doctor_id) &&
    !empty($data->inventory_id) &&
    !empty($data->dosage)
) {
    $prescription->patient_id = $data->patient_id;
    $prescription->doctor_id = $data->doctor_id;
    $prescription->inventory_id = $data->inventory_id;
    $prescription->dosage = $data->dosage;

    if($prescription->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Prescription created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create prescription."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to prescribe. Data is incomplete."));
}
?>
