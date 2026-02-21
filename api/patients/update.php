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
include_once '../../models/Patient.php';

$database = new Database();
$db = $database->getConnection();
$patient = new Patient($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->id) &&
    !empty($data->name) &&
    !empty($data->age) &&
    !empty($data->gender) &&
    !empty($data->phone)
){
    $patient->id = $data->id;
    $patient->name = $data->name;
    $patient->age = $data->age;
    $patient->gender = $data->gender;
    $patient->phone = $data->phone;
    $patient->address = isset($data->address) ? $data->address : "";

    if($patient->update()){
        http_response_code(200);
        echo json_encode(array("message" => "Patient was updated."));
    } else{
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update patient."));
    }
} else{
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update patient. Data is incomplete."));
}
?>
