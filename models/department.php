<?php
  class Department {
    private $conn;
    private $table = 'department';

    public function __construct($db) {
      $this->conn = $db;
    }

    public function addDepart($name) {
      $query = 'INSERT INTO ' . $this->table . ' SET name = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $name);
      if ($stmt->execute()) {
        $id = $this->conn->lastInsertId(); 
        return $id;
      } else {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }

    public function getDeparts() {
      $query = 'SELECT * FROM '. $this->table;
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function updateDepart($id, $name) {
      $query = 'UPDATE ' . $this->table . ' 
                  SET name = ?
                  WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $name);
      $stmt->bindParam(2, $id);
      if ($stmt->execute()) {
        return true;
      } else {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }
  }