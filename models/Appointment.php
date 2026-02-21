<?php
class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $id;
    public $patient_id;
    public $doctor_id;
    public $schedule_date; // DATETIME format: YYYY-MM-DD HH:MM:SS
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Schedule a new appointment
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET patient_id=:patient_id, doctor_id=:doctor_id, 
                      schedule_date=:schedule_date, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->schedule_date = htmlspecialchars(strip_tags($this->schedule_date));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":schedule_date", $this->schedule_date);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get today's appointments for Queue Status
    public function readToday() {
        // Select appointments where schedule_date is today
        // Also Join with patients table to get patient name
        // Join with users table to get doctor name
        $query = "SELECT a.id, a.doctor_id, a.patient_id, a.schedule_date, a.status, 
                         p.name as patient_name, u.name as doctor_name
                  FROM " . $this->table_name . " a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users u ON a.doctor_id = u.id
                  WHERE DATE(a.schedule_date) = CURDATE()
                  ORDER BY a.schedule_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Update appointment status (e.g. to 'Checked-In' or 'Completed')
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . "
                  SET status = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
