<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$response = array(
    "server" => "running",
    "database" => "disconnected",
    "timestamp" => date('c')
);

try {
    $database = new Database();
    $db = $database->getConnection();
    if($db) {
        $response["database"] = "connected";
        http_response_code(200);
    } else {
        http_response_code(503);
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
    http_response_code(503);
}

echo json_encode($response);
?>
