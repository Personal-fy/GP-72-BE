<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Inventory.php';

$database = new Database();
$db = $database->getConnection();

$item = new Inventory($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->id) &&
    !empty($data->name) &&
    !empty($data->quantity)
) {
    $item->id = $data->id;
    $item->name = $data->name;
    $item->quantity = $data->quantity;
    
    // Optional fields with fallbacks if needed, assuming frontend sends them
    $item->category = !empty($data->category) ? $data->category : 'General';
    $item->reorder_level = !empty($data->reorder_level) ? $data->reorder_level : 0;
    $item->unit_price = !empty($data->unit_price) ? $data->unit_price : 0.00;

    if($item->update()) {
        http_response_code(200);
        echo json_encode(array("message" => "Item was updated."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update item."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update item. Data is incomplete."));
}
?>
