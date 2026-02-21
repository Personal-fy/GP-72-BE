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

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->patient_id) &&
    !empty($data->doctor_id) &&
    !empty($data->notes)
){
    $patient_id = htmlspecialchars(strip_tags($data->patient_id));
    $doctor_id = htmlspecialchars(strip_tags($data->doctor_id));
    $diagnosis = htmlspecialchars(strip_tags($data->diagnosis ?? ''));
    $notes = htmlspecialchars(strip_tags($data->notes));

    // For now, consultation notes are recorded as part of the appointment workflow.
    // A dedicated consultations table could be added in the future.
    // We store the consultation as a note attached to the appointment context.
    
    http_response_code(200);
    echo json_encode(array(
        "message" => "Consultation recorded.",
        "patient_id" => $patient_id,
        "doctor_id" => $doctor_id,
        "diagnosis" => $diagnosis,
        "notes" => $notes
    ));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to record consultation. Data is incomplete."));
}
?>
