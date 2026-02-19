<?php
class LabRequest {
    private $conn;
    private $table_name = "lab_requests";

    public $id;
    public $patient_id;
    public $doctor_id;
    public $test_type;
    public $results;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new Lab Request (by Doctor)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET patient_id=:patient_id, doctor_id=:doctor_id, 
                      test_type=:test_type, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->test_type = htmlspecialchars(strip_tags($this->test_type));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":test_type", $this->test_type);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all requests (for Lab Tech Dashboard)
    public function readAll() {
        // Join with patients and doctors for names
        $query = "SELECT l.id, l.test_type, l.status, l.created_at, l.results,
                         p.name as patient_name, u.name as doctor_name
                  FROM " . $this->table_name . " l
                  LEFT JOIN patients p ON l.patient_id = p.id
                  LEFT JOIN users u ON l.doctor_id = u.id
                  ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Update results (by Lab Tech)
    public function updateResult() {
        $query = "UPDATE " . $this->table_name . "
                  SET results = :results, status = 'Completed'
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->results = htmlspecialchars(strip_tags($this->results));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":results", $this->results);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
