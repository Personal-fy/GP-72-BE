<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Prescription.php';

$database = new Database();
$db = $database->getConnection();

$prescription = new Prescription($db);

$patient_id = isset($_GET['id']) ? $_GET['id'] : die();

$stmt = $prescription->readHistory($patient_id);
$num = $stmt->rowCount();

if($num > 0) {
    $history_arr = array();
    $history_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $history_item = array(
            "id" => $id,
            "doctor_name" => $doctor_name,
            "drug_name" => $drug_name,
            "dosage" => $dosage,
            "status" => $status,
            "created_at" => $created_at
        );
        array_push($history_arr["records"], $history_item);
    }
    http_response_code(200);
    echo json_encode($history_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No medical history found for this patient."));
}
?>
