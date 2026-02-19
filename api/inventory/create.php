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
    !empty($data->name) &&
    !empty($data->category) &&
    !empty($data->quantity) &&
    !empty($data->unit_price)
) {
    $item->name = $data->name;
    $item->category = $data->category;
    $item->quantity = $data->quantity;
    $item->reorder_level = isset($data->reorder_level) ? $data->reorder_level : 10;
    $item->unit_price = $data->unit_price;

    if($item->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Medicine created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create medicine."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create. Data is incomplete."));
}
?>
