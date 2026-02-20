<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
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

$patient_id = isset($_GET['id']) ? $_GET['id'] : null;

if($patient_id) {
    $stmt = $prescription->readHistory($patient_id);
} else {
    // If no ID, fetch all history for the doctor to see
    $stmt = $prescription->readAll();
}
$num = $stmt->rowCount();

if($num > 0) {
    $history_arr = array();
    $history_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        $history_item = array(
            "id" => $id,
            "patient_name" => isset($patient_name) ? $patient_name : "N/A", // Only in readAll
            "doctor_name" => $doctor_name,
            "condition" => $drug_name . " (" . $dosage . ")", // Synthesizing condition from drug/dosage for display
            "last_visit" => $created_at,
            "status" => $status
        );
        array_push($history_arr["records"], $history_item);
    }
    http_response_code(200);
    echo json_encode($history_arr["records"]); // Return direct array of records
} else {
    http_response_code(200); // Return 200 with empty array instead of 404 to prevent frontend errors
    echo json_encode(array());
}
?>
