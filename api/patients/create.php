
<?php
// Headers for CORS (Crucial for React!)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Patient.php';

$database = new Database();
$db = $database->getConnection();

$patient = new Patient($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->name) &&
    !empty($data->age) &&
    !empty($data->gender)
) {
    // Set patient property values
    $patient->name = $data->name;
    $patient->age = $data->age;
    $patient->gender = $data->gender;
    $patient->phone = $data->phone;
    $patient->address = $data->address;

    if($patient->create()) {
        http_response_code(201); // Created
        echo json_encode(array("message" => "Patient was registered."));
    } else {
        http_response_code(503); // Service Unavailable
        echo json_encode(array("message" => "Unable to register patient."));
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Unable to register. Data is incomplete."));
}
?>