<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../../config/database.php';
include_once '../../models/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$appointment = new Appointment($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Read scheduled appointments (e.g., today's)
    $stmt = $appointment->readToday();
    $num = $stmt->rowCount();

    if($num > 0) {
        $appointments_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $appointment_item = array(
                "id" => $id,
                "doctor_id" => $row['doctor_id'],
                "patient_id" => $row['patient_id'],
                "schedule_date" => date("Y-m-d H:i", strtotime($schedule_date)),
                "date" => date("Y-m-d", strtotime($schedule_date)),
                "time" => date("H:i", strtotime($schedule_date)),
                "patient_name" => $patient_name,
                "doctor_name" => $doctor_name,
                "type" => "General",
                "status" => $status
            );
            array_push($appointments_arr, $appointment_item);
        }
        http_response_code(200);
        echo json_encode($appointments_arr);
    } else {
        http_response_code(200);
        echo json_encode(array());
    }
}
elseif ($method == 'POST') {
    // Create new appointment
    $data = json_decode(file_get_contents("php://input"));

    if(
        !empty($data->patient_id) &&
        !empty($data->doctor_id) &&
        !empty($data->schedule_date)
    ) {
        $appointment->patient_id = $data->patient_id;
        $appointment->doctor_id = $data->doctor_id;
        $appointment->schedule_date = $data->schedule_date;
        $appointment->status = 'Scheduled';

        if($appointment->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Appointment was scheduled."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to schedule appointment."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to schedule appointment. Data is incomplete."));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Method not allowed."));
}
?>
