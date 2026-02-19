<?php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "hms_group72"; // Change to your DB name
    private $username = "root";       // Default XAMPP user
    private $password = "";           // Default XAMPP password
    private $port = "5222";           // Custom MySQL port
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>