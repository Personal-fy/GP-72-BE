<?php
class Prescription {
    private $conn;
    private $table_name = "prescriptions";
    private $inventory_table = "inventory";
    private $users_table = "users";

    public $id;
    public $patient_id;
    public $doctor_id;
    public $inventory_id;
    public $dosage;
    public $status;
    public $created_at;

    // Joined fields
    public $doctor_name;
    public $drug_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE (Prescribe medication)
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET patient_id=:patient_id, doctor_id=:doctor_id, inventory_id=:inventory_id, dosage=:dosage, status='Pending'";
        
        $stmt = $this->conn->prepare($query);

        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->inventory_id = htmlspecialchars(strip_tags($this->inventory_id));
        $this->dosage = htmlspecialchars(strip_tags($this->dosage));

        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":inventory_id", $this->inventory_id);
        $stmt->bindParam(":dosage", $this->dosage);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ (Get Patient History)
    function readHistory($patient_id) {
        $query = "SELECT p.id, p.dosage, p.status, p.created_at, u.name as doctor_name, i.name as drug_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN " . $this->users_table . " u ON p.doctor_id = u.id
                  LEFT JOIN " . $this->inventory_table . " i ON p.inventory_id = i.id
                  LEFT JOIN patients pat ON p.patient_id = pat.id
                  WHERE p.patient_id = ?
                  ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $patient_id);
        $stmt->execute();
        return $stmt;
    }

    // READ ALL (For Doctor Dashboard)
    function readAll() {
        $query = "SELECT p.id, p.dosage, p.status, p.created_at, u.name as doctor_name, i.name as drug_name, pat.name as patient_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN " . $this->users_table . " u ON p.doctor_id = u.id
                  LEFT JOIN " . $this->inventory_table . " i ON p.inventory_id = i.id
                  LEFT JOIN patients pat ON p.patient_id = pat.id
                  ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
