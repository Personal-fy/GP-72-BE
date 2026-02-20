<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../../config/database.php';
include_once '../../models/Prescription.php';

$database = new Database();
$db = $database->getConnection();

$prescription = new Prescription($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Read all prescriptions
    $stmt = $prescription->readAll();
    $num = $stmt->rowCount();

    if($num > 0) {
        $prescriptions_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $prescription_item = array(
                "id" => $id,
                "date" => date("Y-m-d", strtotime($created_at)),
                "patient_name" => $patient_name,
                "doctor_name" => $doctor_name,
                "medication" => $drug_name,
                "dosage" => $dosage,
                "status" => $status
            );
            array_push($prescriptions_arr, $prescription_item);
        }
        http_response_code(200);
        echo json_encode($prescriptions_arr);
    } else {
        http_response_code(200);
        echo json_encode(array());
    }
}
elseif ($method == 'POST') {
    // Create new prescription
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
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>
