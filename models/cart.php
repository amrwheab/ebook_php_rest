<?php
class Cart {
  // DB stuff
  private $conn;
  private $table = 'cart';

  // Constructor with DB
  public function __construct($db) {
    $this->conn = $db;
  }

  public function getMiniCart($userId) {
    $query = 'SELECT * From ' . $this->table . ' WHERE user_id = ?';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $userId);
    $stmt->execute();
    return $stmt;
  }

  public function getFullCart($userId) {
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
              price,
              c.id as cart_id, buyed
              From ' . $this->table . ' c
                LEFT JOIN book b
                  ON b.id = c.book_id 
                WHERE user_id = ?
                ';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $userId);
    $stmt->execute();
    return $stmt;
  }

  public function getOneCart($userId, $bookId) {
    $query = 'SELECT * From ' . $this->table . ' WHERE user_id = ? && book_id = ?';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $userId);
    $stmt->bindParam(2, $bookId);
    $stmt->execute();
    return $stmt;
  }

  public function getCartCount($userId) {
    $query = 'SELECT count(*) From ' . $this->table . ' WHERE user_id = '.$userId;
    $stmt = $this->conn->query($query);
    
    return $stmt->fetchColumn();
  }

  public function makeBuy($userId, $bookId) {
    $query = 'UPDATE '. $this->table . ' 
                SET buyed = 1
                WHERE user_id = :user_id && book_id = :book_id
                ';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':book_id', $bookId);
    if ($stmt->execute()) {
      return true;
    } else {
      printf('error', $stmt->error);
      return false;
    }
  }

  public function addToCart($bookId, $userId, $buyed) {
    $query = 'INSERT INTO '. $this->table . ' 
                SET user_id = :user_id,
                    book_id = :book_id,
                    buyed = :buyed
                ';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':book_id', $bookId);
    $stmt->bindParam(':buyed', $buyed);
    if ($stmt->execute()) {
      return true;
    } else {
      printf('error', $stmt->error);
      return false;
    }
  }


  public function removeFromCart($bookId, $userId) {
    $query = 'DELETE FROM ' . $this->table . ' 
              WHERE user_id = :user_id && book_id = :book_id';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':book_id', $bookId);
    if ($stmt->execute()) {
      return true;
    } else {
      printf('error', $stmt->error);
      return false;
    }
  }


}