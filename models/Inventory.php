<?php
class Inventory {
    private $conn;
    private $table_name = "inventory";

    public $id;
    public $name;
    public $category;
    public $quantity;
    public $reorder_level;
    public $unit_price;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // READ (List all medicines)
    function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // CREATE (Add new medicine)
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, category=:category, quantity=:quantity, reorder_level=:reorder_level, unit_price=:unit_price";
        
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->reorder_level = htmlspecialchars(strip_tags($this->reorder_level));
        $this->unit_price = htmlspecialchars(strip_tags($this->unit_price));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":reorder_level", $this->reorder_level);
        $stmt->bindParam(":unit_price", $this->unit_price);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DISPENSE (Reduce stock)
    function dispense($id, $qty) {
        // First check current stock
        $query = "SELECT quantity FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && $row['quantity'] >= $qty) {
            $new_qty = $row['quantity'] - $qty;
            $updateQuery = "UPDATE " . $this->table_name . " SET quantity = :quantity WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":quantity", $new_qty);
            $updateStmt->bindParam(":id", $id);
            
            if($updateStmt->execute()) {
                return true;
            }
        }
        return false;
    }
}
?>