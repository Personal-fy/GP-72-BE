<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../config/database.php';
include_once '../../models/LabRequest.php';

$database = new Database();
$db = $database->getConnection();

$lab = new LabRequest($db);

$stmt = $lab->readAll();
$num = $stmt->rowCount();

if($num > 0) {
    $labs_arr = array();
    $labs_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $lab_item = array(
            "id" => $id,
            "patient_name" => $patient_name,
            "doctor_name" => $doctor_name,
            "test_type" => $test_type,
            "status" => $status,
            "results" => $results,
            "created_at" => $created_at
        );

        array_push($labs_arr["records"], $lab_item);
    }

    http_response_code(200);
    echo json_encode($labs_arr);
} else {
    http_response_code(200);
    echo json_encode(array("records" => []));
}
?>
