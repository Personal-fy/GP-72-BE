<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../../config/database.php';
include_once '../../models/Bill.php';

$database = new Database();
$db = $database->getConnection();

$bill = new Bill($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Read all bills
    $stmt = $bill->readAll();
    $num = $stmt->rowCount();

    $bills_arr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bill_item = array(
            "id" => $row['id'],
            "patient_name" => $row['patient_name'],
            "patient_id" => $row['patient_id'],
            "appointment_id" => $row['appointment_id'],
            "consultation_fee" => floatval($row['consultation_fee']),
            "pharmaceutical_total" => floatval($row['pharmaceutical_total']),
            "lab_total" => floatval($row['lab_total']),
            "total_amount" => floatval($row['total_amount']),
            "status" => $row['status'],
            "notes" => $row['notes'],
            "date" => date("Y-m-d", strtotime($row['created_at']))
        );
        array_push($bills_arr, $bill_item);
    }

    http_response_code(200);
    echo json_encode($bills_arr);

} elseif ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Check if it's a status update or new bill generation
    if (!empty($data->id) && !empty($data->status)) {
        // Update bill status
        $bill->id = $data->id;
        $bill->status = $data->status;

        if ($bill->updateStatus()) {
            http_response_code(200);
            echo json_encode(array("message" => "Bill status updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update bill status."));
        }
    } elseif (!empty($data->patient_id)) {
        // Generate new bill
        $appointment_id = !empty($data->appointment_id) ? $data->appointment_id : null;
        $result = $bill->generate($data->patient_id, $appointment_id);

        if ($result) {
            http_response_code(201);
            echo json_encode(array(
                "message" => "Bill generated successfully.",
                "bill" => $result
            ));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to generate bill."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data. Provide patient_id or bill id+status."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>
