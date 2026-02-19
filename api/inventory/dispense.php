<?php
// ... headers (allow POST) ...
include_once '../../config/database.php';
include_once '../../models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

$data = json_decode(file_get_contents("php://input"));

// Pharmacist sends { "drug_id": 5, "quantity": 2 }
if(!empty($data->drug_id) && !empty($data->quantity)) {
    
    if($inventory->dispense($data->drug_id, $data->quantity)) {
        
        // Success: Logic to mark prescription as 'Dispensed' would go here too
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