<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$appointment = new Appointment($db);

$stmt = $appointment->readToday();
$num = $stmt->rowCount();

if($num > 0) {
    $appointments_arr = array();
    $appointments_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $appointment_item = array(
            "id" => $id,
            "patient_name" => $patient_name,
            "doctor_name" => $doctor_name,
            "schedule_date" => $schedule_date,
            "status" => $status
        );

        array_push($appointments_arr["records"], $appointment_item);
    }

    http_response_code(200);
    echo json_encode($appointments_arr);
} else {
    http_response_code(200); // OK, but empty
    echo json_encode(array("records" => [])); // Return empty array instead of 404 message for easier frontend handling
}
?>
