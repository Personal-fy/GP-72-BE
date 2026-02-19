<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get current database name
    $stmt_db = $db->query("SELECT DATABASE()");
    $db_name = $stmt_db->fetchColumn();

    // Get all tables
    $stmt_tables = $db->query("SHOW TABLES");
    $tables = $stmt_tables->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(array(
        "status" => "success",
        "connected_to_db" => $db_name,
        "tables_found" => $tables
    ));

} catch(PDOException $e) {
    http_response_code(503);
    echo json_encode(array(
        "status" => "error",
        "message" => "Connection failed: " . $e->getMessage()
    ));
}
?>
