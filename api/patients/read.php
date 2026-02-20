<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
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

$stmt = $patient->read();
$num = $stmt->rowCount();

if($num > 0){
    $patients_arr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $patient_item = array(
            "id" => $id,
            "name" => $name,
            "age" => $age,
            "gender" => $gender,
            "phone" => $phone,
            "address" => $address,
            "created_at" => $created_at
        );
        array_push($patients_arr, $patient_item);
    }
    http_response_code(200);
    echo json_encode($patients_arr);
} else {
    http_response_code(200);
    echo json_encode(array());
}
?>
