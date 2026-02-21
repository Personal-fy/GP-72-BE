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

if (!empty($data->id)) {
    $patient->id = $data->id;

    if ($patient->delete()) {
        http_response_code(200);
        echo json_encode(array("message" => "Patient was deleted."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete patient."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to delete patient. Data is incomplete."));
}
?>
