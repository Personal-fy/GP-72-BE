<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/LabRequest.php';

$database = new Database();
$db = $database->getConnection();

$lab = new LabRequest($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->id) &&
    !empty($data->results)
) {
    $lab->id = $data->id;
    $lab->results = $data->results;

    if($lab->updateResult()) {
        http_response_code(200);
        echo json_encode(array("message" => "Lab results updated."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update results."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
