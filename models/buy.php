<?php
  class Buy {
    private $conn;
    private $table = 'buy';

  // Constructor with DB
  public function __construct($db) {
    $this->conn = $db;
  }

  public function addBuy($userId, $bookIds) {
    $addCheck = true;
    for ($i = 0; $i < count($bookIds); $i++) {
      $query = 'INSERT INTO '. $this->table . ' 
                  SET user_id = :user_id,
                      book_id = :book_id
                  ';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $userId);
      $stmt->bindParam(':book_id', $bookIds[$i]);
      if (!$stmt->execute()) {
        global $addCheck;
        $addCheck = false;
        break;
        printf('error', $stmt->error);
        return false;
      }
    }
    return $addCheck;
  }

  public function getMiniBuyed($bookId, $userId) {
    $query = 'SELECT * FROM ' . $this->table . ' 
                WHERE book_id = ? && user_id = ?';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $bookId);
    $stmt->bindParam(2, $userId);
    $stmt->execute();
    return $stmt->fetch();
  }

  }