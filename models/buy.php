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
    return $stmt;
  }
  
  public function getFullBuyed($userId, $page) {
    $limit = 20;
    $total_skip = ((int)$page-1)*20;

    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
    price
                FROM ' . $this->table . ' bu
                LEFT JOIN book b ON b.id = bu.book_id
                WHERE bu.user_id = ?
                LIMIT '. $total_skip.', '. $limit;
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $userId);
                $stmt->execute();
                return $stmt;
  }
              
  public function getCountBuyed($userId) {
    $query = 'SELECT count(*) FROM ' . $this->table . ' 
                WHERE user_id = '. $userId;
    $stmt = $this->conn->query($query)->fetchColumn();
    return $stmt;
  }
}