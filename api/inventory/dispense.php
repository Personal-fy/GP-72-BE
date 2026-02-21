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
include_once '../../models/Inventory.php';
include_once '../../models/Prescription.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);
$prescription = new Prescription($db);

$data = json_decode(file_get_contents("php://input"));

// Pharmacist sends { "prescription_id": 1, "drug_id": 5, "quantity": 1 }
if(!empty($data->drug_id) && !empty($data->quantity)) {
    
    if($inventory->dispense($data->drug_id, $data->quantity)) {
        
        // Also mark prescription as 'Dispensed' if prescription_id is provided
        if(!empty($data->prescription_id)) {
            $stmt = $db->prepare("UPDATE prescriptions SET status = 'Dispensed' WHERE id = :id");
            $stmt->bindParam(":id", $data->prescription_id);
            $stmt->execute();
        }
        
        http_response_code(200);
        echo json_encode(array("message" => "Drug dispensed. Stock updated."));
        
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Failed. Insufficient stock."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>