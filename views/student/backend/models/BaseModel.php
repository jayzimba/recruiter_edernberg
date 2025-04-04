<?php
require_once __DIR__ . '/../config/database.php';

abstract class BaseModel {
    protected $conn;
    protected $table_name;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function findAll($conditions = [], $order = '', $limit = '') {
        $sql = "SELECT * FROM " . $this->table_name;
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', array_map(
                function($key) { return "$key = :$key"; },
                array_keys($conditions)
            ));
        }
        
        if ($order) {
            $sql .= " ORDER BY " . $order;
        }
        
        if ($limit) {
            $sql .= " LIMIT " . $limit;
        }

        $stmt = $this->conn->prepare($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findOne($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO " . $this->table_name . " ($fields) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE " . $this->table_name . " SET $fields WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?> 