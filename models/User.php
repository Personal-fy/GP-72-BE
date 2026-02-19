<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $username;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register User
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, username=:username, password=:password, role=:role";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $this->role);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            // Check for duplicate entry (SQLSTATE 23000)
            if ($e->getCode() == 23000) {
                 // Log error or set a specific error message property if needed
                 // For now returning false will trigger the generic error in register.php
                 // But ideally register.php should check for username existence first or we return a specific error code.
                 return false; 
            }
            return false;
        }
    }

    // Login User
    function login() {
        $query = "SELECT id, name, role, password FROM " . $this->table_name . " WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }
}
?>
