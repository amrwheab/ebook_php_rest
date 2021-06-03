<?php
  class Rate {
    private $conn;
    private $table = 'rate';

    public function __construct($db) {
      $this->conn = $db;
    }

    public function addRateToBook($value, $bookId) {
      $query = 'UPDATE book SET rateNum = rateNum + 1, rate = rate + ? WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $value);
      $stmt->bindParam(2, $bookId);
      if ($stmt->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function deleteRateFromBook($value, $bookId) {
      $query = 'UPDATE book SET rateNum = rateNum - 1, rate = rate - ? WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $value);
      $stmt->bindParam(2, $bookId);
      if ($stmt->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function checkRate($userId, $bookId) {
      $query = 'SELECT * FROM ' . $this->table . ' WHERE userId = ? && bookId = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $userId);
      $stmt->bindParam(2, $bookId);
      $stmt->execute();
      return $stmt->fetch();
    }

    public function addRate($userId, $bookId, $value) {
      $query = 'INSERT INTO ' . $this->table . ' SET userId = ?, bookId = ?, value = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $userId);
      $stmt->bindParam(2, $bookId);
      $stmt->bindParam(3, $value);

      if ($stmt->execute() && $this->addRateToBook($value, $bookId)) {
        return true;
      } else {
        return false;
      }
    }

    public function updateRate($userId, $bookId, $value, $prevRate) {
      $query = 'UPDATE ' . $this->table . ' SET value = ? WHERE userId = ? && bookId = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $value);
      $stmt->bindParam(2, $userId);
      $stmt->bindParam(3, $bookId);
      if ($stmt->execute() && $this->deleteRateFromBook($prevRate, $bookId) && $this->addRateToBook($value, $bookId)) {
        return true;
      } else {
        return false;
      }
    }

    public function getRate($bookId) {
      $query = 'SELECT * FROM ' . $this->table . ' WHERE bookId = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $bookId);
      $stmt->execute();
      return $stmt;
    }

    public function deleteRate($userId, $bookId, $value) {
      $query = 'DELETE FROM ' . $this->table . ' WHERE userId = ? && bookId = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $userId);
      $stmt->bindParam(2, $bookId);
      if ($stmt->execute() && $this->deleteRateFromBook($value, $bookId)) {
        return true;
      } else {
        return false;
      }
    }
  }