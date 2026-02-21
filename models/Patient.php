<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public $id;
    public $name;
    public $age;
    public $gender;
    public $phone;
    public $address;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE (Register Patient)
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, age=:age, gender=:gender, phone=:phone, address=:address";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ (Get All Patients)
    function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ (Search Patients)
    function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name LIKE ? OR phone LIKE ?";
        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->execute();
        return $stmt;
    }

    // UPDATE (Edit Patient)
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, age=:age, gender=:gender, phone=:phone, address=:address
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE (Remove Patient)
    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>