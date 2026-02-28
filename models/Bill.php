<?php
class Bill {
    private $conn;
    private $table_name = "bills";

    public $id;
    public $patient_id;
    public $appointment_id;
    public $consultation_fee;
    public $pharmaceutical_total;
    public $lab_total;
    public $total_amount;
    public $status;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Generate bill for a patient by computing costs from prescriptions + lab requests
    public function generate($patient_id, $appointment_id = null) {
        // 1. Consultation Fee (flat rate)
        $consultation_fee = 5000.00;

        // 2. Pharmaceutical cost: SUM of prescribed medication prices
        $pharmaQuery = "SELECT COALESCE(SUM(i.unit_price), 0) as pharma_total
                        FROM prescriptions p
                        LEFT JOIN inventory i ON p.inventory_id = i.id
                        WHERE p.patient_id = :patient_id";
        $stmt = $this->conn->prepare($pharmaQuery);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        $pharmaRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $pharmaceutical_total = floatval($pharmaRow['pharma_total']);

        // 3. Lab cost: COUNT of lab requests × flat rate per test (₦3,000 per test)
        $labQuery = "SELECT COUNT(*) as lab_count
                     FROM lab_requests
                     WHERE patient_id = :patient_id";
        $stmt = $this->conn->prepare($labQuery);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        $labRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $lab_total = intval($labRow['lab_count']) * 3000.00;

        // 4. Total
        $total_amount = $consultation_fee + $pharmaceutical_total + $lab_total;

        // 5. Insert bill
        $query = "INSERT INTO " . $this->table_name . "
                  SET patient_id = :patient_id,
                      appointment_id = :appointment_id,
                      consultation_fee = :consultation_fee,
                      pharmaceutical_total = :pharmaceutical_total,
                      lab_total = :lab_total,
                      total_amount = :total_amount,
                      status = 'Unpaid'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":appointment_id", $appointment_id);
        $stmt->bindParam(":consultation_fee", $consultation_fee);
        $stmt->bindParam(":pharmaceutical_total", $pharmaceutical_total);
        $stmt->bindParam(":lab_total", $lab_total);
        $stmt->bindParam(":total_amount", $total_amount);

        if($stmt->execute()) {
            return array(
                "id" => $this->conn->lastInsertId(),
                "consultation_fee" => $consultation_fee,
                "pharmaceutical_total" => $pharmaceutical_total,
                "lab_total" => $lab_total,
                "total_amount" => $total_amount
            );
        }
        return false;
    }

    // Read all bills
    public function readAll() {
        $query = "SELECT b.id, b.consultation_fee, b.pharmaceutical_total, b.lab_total,
                         b.total_amount, b.status, b.notes, b.created_at,
                         p.name as patient_name, b.patient_id, b.appointment_id
                  FROM " . $this->table_name . " b
                  LEFT JOIN patients p ON b.patient_id = p.id
                  ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single bill
    public function readOne($id) {
        $query = "SELECT b.id, b.consultation_fee, b.pharmaceutical_total, b.lab_total,
                         b.total_amount, b.status, b.notes, b.created_at,
                         p.name as patient_name, b.patient_id, b.appointment_id
                  FROM " . $this->table_name . " b
                  LEFT JOIN patients p ON b.patient_id = p.id
                  WHERE b.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update bill status (Paid / Partial)
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
